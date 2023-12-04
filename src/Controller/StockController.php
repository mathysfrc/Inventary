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

            $sku = $stockRepository->findBy([], ['SKU' => 'ASC']);
        }

        return $this->render('stock/index.html.twig', [
            'stocks' => $sku,
            'form' => $form,
        ]);
    }

    #[Route('/delete-all-data', name: 'delete_all_data')]
    public function deleteAllData(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les entrées dans la table Stock
        $stocks = $entityManager->getRepository(Stock::class)->findAll();

        foreach ($stocks as $stock) {
            // Supprimer chaque entité stock
            $entityManager->remove($stock);
        }

        // Appliquer les suppressions en base de données
        $entityManager->flush();

        return $this->redirectToRoute('app_stock_index');
    }


    #[Route('/new', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dateTime = new DateTime();
        $dateTime->modify('+2 hours');

        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->remove('SKU');
        $form->handleRequest($request);

        $createdStocks = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $generatedSKU = Stock::generateSKU($stock, $entityManager);
            $stock->setSKU($generatedSKU);

            $quantity = $request->request->get('quantity');

            // Persistez l'élément d'origine avant la boucle
            $tracking = Tracking::getTrackingFromStock($stock, 'Initialisation', $dateTime);
            $entityManager->persist($tracking);
            $entityManager->persist($stock);
            $entityManager->flush();  // Flush après la persistance initiale

            // Générez à nouveau le SKU après flush

            // Dupliquez l'élément en fonction de la quantité
            for ($i = 1; $i < $quantity; $i++) {
                $duplicateStock = clone $stock;
                $generatedSKU = Stock::generateSKU($duplicateStock, $entityManager);
                $duplicateStock->setSKU($generatedSKU);

                // Mettez à jour d'autres propriétés si nécessaire
                $tracking = Tracking::getTrackingFromStock($duplicateStock, 'Duplication', $dateTime);
                $entityManager->persist($tracking);
                $entityManager->persist($duplicateStock);  // Flush après chaque duplication

                // Générez à nouveau le SKU après flush
                $generatedSKU = Stock::generateSKU($duplicateStock, $entityManager);
                $duplicateStock->setSKU($generatedSKU);
                $createdStocks[] = $duplicateStock;

                // Flush à nouveau après la mise à jour du SKU
                $entityManager->flush();
            }
            $entityManager->flush();

            $id = $stock->getId();


            return $this->redirectToRoute('app_stock_reference', ['id' => $id, 'index' => 1, 'max' => $quantity], Response::HTTP_SEE_OTHER);

        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
        ]);
    }

    #[Route('/update', name: 'app_stock_update', methods: ['GET', 'POST'])]
    public function update(Request $request, StockRepository $stockRepository, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the parameters from the PUT request
        // Try to convert them into "int", 0 if fail
        $size1restant = intval($request->request->get('size1restant'));
        $size2restant = intval($request->request->get('size2restant'));
        $size1consommation = intval($request->request->get('size1consommation'));
        $size2consommation = intval($request->request->get('size2consommation'));
        $sku = $request->request->get('SKU');
        $consommationMode = $request->request->get('consommationMode'); // On reçois "external" ou "internal"


        // Retrieve the object from the database and update it
        $stock = $stockRepository->findOneBy(['SKU' => $sku]);

        $productFamily = $stock->getProductFamily();

        if ($size1restant != 0 && $size2restant != 0) {
            $size1 = $size1restant;
            $size2 = $size2restant;
        } else if ($size1consommation >= 0 && $size2consommation >= 0 && $size1restant === 0 && $size2restant === 0) {
            $size1 = $stock->getSize1() - $size1consommation;
            $size2 = $stock->getSize2() - $size2consommation;
        } else {
            return $this->render('error/index.html.twig', [
                'error' => 'Veuillez rentrer les deux valeurs consommées ou restantes.'
            ]);
        }

        // Check if the new values are greater than or equal to the old values
        if ($size1 > $stock->getSize1() || $size2 > $stock->getSize2() || $size1 <= 0 || $size2 <= 0) {
            if ($size1 === 0 || $size2 === 0) {
                return $this->render('error/index.html.twig', [
                    'error' => 'Merci de scanner la référence dans la section "Consommation totale".'
                ]);
            }

            return $this->render('error/index.html.twig', [
                'error' => 'Les nouvelles valeurs sont supérieures aux anciennes, merci de ressaisir une valeur.'
            ]);
        }

        $oldSize1 = $stock->getSize1();
        $oldSize2 = $stock->getSize2();


        // Update the stock object
        if (($oldSize1 != $size1 || $oldSize2 != $size2) && $consommationMode == "internal") {
            // Si on a changé longueur et largeur, on a consommé un morceau de plaque
            // Le size 1 et 2 de l'objet dans la BDD ne changent pas !!!!!!!!!!!!!!!!
            // Seule la surface change (et on renseigne une forme)

            // Calcul de la nouvelle surface après consommation interne
            $surfaceConsommee = $size1consommation * $size2consommation;
            // var_dump($surfaceConsommee);

            // if ( si jamais surface == 0 alors faire le calcul au sinon reprendre les valeurs du stock)
            // création $surfaceActuelle = -> getSize1 x getSize2 si jamais surface est nulle au sinon elle est égale a surface

            if ($stock->getSurface() == null) {
                $surfaceActuelle = $stock->getSize1() * $stock->getSize2();
            } else {
                $surfaceActuelle = $stock->getSurface();
            }


            $surfaceRestante = ($surfaceActuelle) - $surfaceConsommee;
            // var_dump($surfaceRestante);
            // Mise à jour de la surface de stock
            $stock->setSurface($surfaceRestante);

            // Mise à jour des valeurs size1 et size2 si nécessaire
            if ($surfaceRestante != ($oldSize1 * $oldSize2)) {
                $stock->setSize1($oldSize1);
                $stock->setSize2($oldSize2);
            } else {
                // Les tailles n'ont pas changé, on met à jour les valeurs de size1 et size2
                $stock->setSize1($size1);
                $stock->setSize2($size2);
            }
        } else {
            // Les tailles n'ont pas changé, on met à jour les valeurs de size1 et size2
            $stock->setSize1($size1);
            $stock->setSize2($size2);
        }

        // Create a DateTime object
        $dateTime = new DateTime();
        $dateTime->modify('+2 hours');

        // Create a tracking entry and persist it
        if ($consommationMode == 'internal') {
            $tracking = Tracking::getTrackingFromStock($stock, 'Consommation interne', $dateTime);
        } else {
            $tracking = Tracking::getTrackingFromStock($stock, 'Consommation externe', $dateTime);
        }
        $entityManager->persist($tracking);

        // Save the changes to the database
        $entityManager->flush();

        // Redirect to another route
        return $this->redirectToRoute('app_partial_conso_imprim', [
            'SKU' => $stock->getSKU(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/partial-conso/{SKU}', name: 'app_partial_conso_imprim')]
    public function conso(Request $request, string $SKU, StockRepository $stockRepository): Response
    {
        // on récupère le stock dans la BDD 
        $stock = $stockRepository->findOneBy([
            'SKU' => $SKU,
        ]);

        if ($stock === null) {
            return $this->render('error/index.html.twig', [
                'error' => 'Le SKU mentionné n\'est pas trouvé'
            ]);
        }

        // on prépare le str qu'on encodera dans le QRCode
        $dataArray = [$stock->getSKU(), $stock->getDescription(), $stock->getProductFamily(), $stock->getReference(), $stock->getPrice(), $stock->getSize1Name(), $stock->getSize1(), $stock->getSize1Unit(), $stock->getSize2Name(), $stock->getSize2(), $stock->getSize2Unit()];
        $dataToEncode = implode("\t", $dataArray);

        // Create QR code
        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        // on encode la string
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Validate the result
        $writer->validateResult($result, $dataToEncode);
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        $dataUri = $result->getDataUri();



        return $this->render('partial_conso_imprim/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
            'dataUri' => $dataUri,
        ]);
    }


    #[Route('/{SKU}/impress', name: 'app_stock_print_SKU')]
    public function printSKU(Request $request, Stock $stock): Response
    {
        return $this->render('print/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
        ]);
    }

    #[Route('/{id}/print', name: 'app_stock_print_with_id')]
    public function print(Request $request, Stock $stock): Response
    {
        return $this->render('print/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
        ]);
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
            // Récupérez l'objet Stock du formulaire
            $stock = $form->getData();

            $size1Manual = $request->request->get('size1Manual');
            $size2Manual = $request->request->get('size2Manual');

            // Récupérez la valeur de "size1ManualValue" depuis la demande
            $size1ManualValue = $request->request->get('size1ManualValue');
            $size2ManualValue = $request->request->get('size2ManualValue');

            // Vérifiez si "size1ManualValue" est un nombre (entier ou float) avant de le mettre à jour
            if ($size1Manual && is_numeric($size1ManualValue)) {
                $stock->setSize1(floatval($size1ManualValue));
            }

            // Vérifiez si "size2ManualValue" est un nombre (entier ou float) avant de le mettre à jour
            if ($size2Manual && is_numeric($size2ManualValue)) {
                $stock->setSize2(floatval($size2ManualValue));
            }

            $dateTime = new DateTime();
            $dateTime->modify('+2 hours');

            // Préparez la ligne de tracking à injecter dans la BDD
            $tracking = new Tracking();
            $tracking->setSKU($stock->getSKU());
            $tracking->setDescription($stock->getDescription());
            $tracking->setSize1($stock->getSize1());
            $tracking->setSize2($stock->getSize2());
            $tracking->setSize1Unit($stock->getSize1Unit());
            $tracking->setSize2Unit($stock->getSize2Unit());
            $tracking->setSize1Name($stock->getSize1Name());
            $tracking->setSize2Name($stock->getSize2Name());
            $tracking->setPrice($stock->getPrice());
            $tracking->setProductFamily($stock->getProductFamily());
            $tracking->setReference($stock->getReference());
            $tracking->setSurface($stock->getSurface());
            $tracking->setShape($stock->getShape());
            $tracking->setFree($stock->getFree());
            $tracking->setComment($stock->getComment());
            $tracking->setStatus($stock->getStatus());
            $tracking->setMovementType('Édition manuelle');
            $tracking->setTimestamp($dateTime);

            $entityManager->persist($tracking);

            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            $id = $stock->getId();

            return $this->redirectToRoute('app_stock_reference', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/edit.html.twig', [
            'stock' => $stock,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->request->get('_token'))) {

            $tracking = new Tracking();
            $tracking->setSKU($stock->getSKU());
            $tracking->setDescription($stock->getDescription());
            $tracking->setSize1($stock->getSize1());
            $tracking->setSize2($stock->getSize2());
            $tracking->setSize1Unit($stock->getSize1Unit());
            $tracking->setSize2Unit($stock->getSize2Unit());
            $tracking->setSize1Name($stock->getSize1Name());
            $tracking->setSize2Name($stock->getSize2Name());
            $tracking->setPrice($stock->getPrice());
            $tracking->setProductFamily($stock->getProductFamily());
            $tracking->setReference($stock->getReference());
            $tracking->setSurface($stock->getSurface());
            $tracking->setShape($stock->getShape());
            $tracking->setFree($stock->getFree());
            $tracking->setComment($stock->getComment());
            $tracking->setStatus($stock->getStatus());
            $tracking->setMovementType('Suppression manuelle');
            $tracking->setTimestamp(new \DateTime());

            $entityManager->persist($tracking);

            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{id}/reference', name: 'app_stock_reference')]
    public function reference(Request $request, Stock $stock): Response
    {

        $index = $request->query->get('index');
        $max = $request->query->get('max');
        $idIncrement = $stock->getId();

        $idIncrement += 1;






        $dataArray = [
            $stock->getSKU(),
            $stock->getDescription(),
            $stock->getProductFamily(),
            $stock->getReference(),
            $stock->getPrice(),
            $stock->getSize1Name(),
            $stock->getSize1(),
            $stock->getSize1Unit(),
            $stock->getSize2Name(),
            $stock->getSize2(),
            $stock->getSize2Unit(),
            $stock->getStatus(),
        ];

        $dataToEncode = implode("\t", $dataArray);

        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        $result = $writer->write($qrCode);
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();

        return $this->render('reference/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $stock,
            'dataUri' => $dataUri,
            'index' => $index,
            'max' => $max,
            'incremented_id' => $idIncrement,

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


        // $writer->validateResult($result, $dataToEncode);
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
}