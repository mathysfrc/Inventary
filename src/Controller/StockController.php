<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Tracking;
use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/stock')]
class StockController extends AbstractController
{
    #[Route('/', name: 'app_stock_index')]
    public function index(Request $request, StockRepository $stockRepository): Response
    {

        $form = $this->createForm(SearchSkuType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $search = $form->getData()['search'];

            $sku = $stockRepository->findLikeName($search);
        } else {

            $sku = $stockRepository->findAll();
        }

        return $this->render('stock/index.html.twig', [
            'stocks' => $sku,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // on prépare la ligne tracking a injecter dans la BDD
            $tracking = new Tracking();
            $tracking->setSKU($stock->getSKU());
            $tracking->setDescription($stock->getDescription());
            $tracking->setSize1($stock->getSize1());
            $tracking->setSize2($stock->getSize2());
            $tracking->setSize1Unit($stock->getSize1Unit());
            $tracking->setSize2Unit($stock->getSize2Unit());
            $tracking->setSize1Name($stock->getSize1Name());
            $tracking->setSize2Name($stock->getSize2Name());
            $tracking->setResultUnit($stock->getResultUnit());
            $tracking->setPrice($stock->getPrice());
            $tracking->setProductFamily($stock->getProductFamily());
            $tracking->setReference($stock->getReference());
            $tracking->setFree($stock->getFree());
            $tracking->setComment($stock->getComment());
            $tracking->setStatus($stock->getStatus());
            $tracking->setMovementType('initialisation');
            $tracking->setTimestamp(new \DateTime());


            $entityManager->persist($tracking);
            // on envoie en BDD le stock du formulaire
            $entityManager->persist($stock);
            $entityManager->flush();
            $id = $stock->getId();

            return $this->redirectToRoute('app_stock_reference', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/update', name: 'app_stock_update', methods: ['POST'])]
    public function update(Request $request, StockRepository $stockRepository, EntityManagerInterface $entityManager): Response
    {
        // on recupère les paramètres dans notre form de type PUT
        $size1 = $request->request->get('size1');
        $size2 = $request->request->get('size2');
        $sku = $request->request->get('SKU');
        // on recupère l'objet en BDD et on le met à jour
        $stock = $stockRepository->findOneBy(['SKU' => $sku]);
        $stock->setSize1($size1);
        $stock->setSize2($size2);

        // on rajoute les lignes de tracking pour dire que l'on a consommé telle référence

        $tracking = Tracking::getTrackingFromStock($stock, 'Consommation', new DateTime());


        $entityManager->persist($tracking);

        // on envoie en BDD le stock du formulaire
        $entityManager->flush();


        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on prépare la ligne tracking a injecter dans la BDD
            $tracking = new Tracking();
            $tracking->setSKU($stock->getSKU());
            $tracking->setDescription($stock->getDescription());
            $tracking->setSize1($stock->getSize1());
            $tracking->setSize2($stock->getSize2());
            $tracking->setSize1Unit($stock->getSize1Unit());
            $tracking->setSize2Unit($stock->getSize2Unit());
            $tracking->setSize1Name($stock->getSize1Name());
            $tracking->setSize2Name($stock->getSize2Name());
            $tracking->setResultUnit($stock->getResultUnit());
            $tracking->setPrice($stock->getPrice());
            $tracking->setProductFamily($stock->getProductFamily());
            $tracking->setReference($stock->getReference());
            $tracking->setFree($stock->getFree());
            $tracking->setComment($stock->getComment());
            $tracking->setStatus($stock->getStatus());
            $tracking->setMovementType('Edition manuelle');
            $tracking->setTimestamp(new \DateTime());


            $entityManager->persist($tracking);

            // on envoie en BDD le stock du formulaire
            $entityManager->flush();
            $id = $stock->getId();


            return $this->redirectToRoute('app_stock_reference', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{id}/reference', name: 'app_stock_reference')]
    public function reference(Request $request, Stock $stock): Response

    {

        $dataArray = [
            $stock->getSKU(), $stock->getDescription(), $stock->getProductFamily(), $stock->getReference(), $stock->getPrice(), $stock->getSize1Name(), $stock->getSize1(), $stock->getSize1Unit(), $stock->getSize2Name(), $stock->getSize2(), $stock->getSize2Unit(), $stock->getStatus(),
            $stock->getResultUnit()
        ];

        $dataToEncode = implode("\t", $dataArray);

        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        $result = $writer->write($qrCode);

        // Validate the result
        $writer->validateResult($result, $dataToEncode);
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();

        return $this->render('reference/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
            'dataUri' => $dataUri,

        ]);
    }

    #[Route('/{id}/reference/create', name: 'app_stock_create')]
    public function noImpression(Request $request, Stock $stock): Response
    {
        $dataArray = [$stock->getSKU(), $stock->getDescription(), $stock->getProductFamily(), $stock->getReference(), $stock->getPrice(), $stock->getSize1Name(), $stock->getSize1(), $stock->getSize1Unit(), $stock->getSize2Name(), $stock->getSize2(), $stock->getSize2Unit()];

        $dataToEncode = implode("\t", $dataArray);

        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        $result = $writer->write($qrCode);

        // Validate the result
        $writer->validateResult($result, $dataToEncode);
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        $dataUri = $result->getDataUri();

        return $this->render('no_impression/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
            'dataUri' => $dataUri,
        ]);
    }

    #[Route('/{id}/reference/impression', name: 'app_stock_impression')]
    public function impression(Request $request, Stock $stock): Response
    {
        $dataArray = [$stock->getSKU(), $stock->getDescription(), $stock->getProductFamily(), $stock->getReference(), $stock->getPrice(), $stock->getSize1Name(), $stock->getSize1(), $stock->getSize1Unit(), $stock->getSize2Name(), $stock->getSize2(), $stock->getSize2Unit()];

        $dataToEncode = implode("\t", $dataArray);

        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        $result = $writer->write($qrCode);

        // Validate the result
        $writer->validateResult($result, $dataToEncode);
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        $dataUri = $result->getDataUri();

        return $this->render('impression/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
            'dataUri' => $dataUri,
        ]);
    }

    #[Route('/{id}/print', name: 'app_stock_print')]
    public function print(Request $request, Stock $stock): Response
    {
        return $this->render('print/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
        ]);
    }
}
