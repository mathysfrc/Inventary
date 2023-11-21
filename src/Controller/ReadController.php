<?php

namespace App\Controller;

use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\StockController;
use App\Entity\Stock;
use App\Entity\Tracking;
use App\Repository\TrackingRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReadController extends AbstractController
{

    #[Route('/read', name: 'app_read')]
    public function index(Request $request, TrackingRepository $trackingRepository, EntityManagerInterface $entityManager, Stock $stock): Response
    {
        //on récupère le SKU a partir de la variable de notre requête POST
        $SKU = $request->request->get('SKU');

        // on recupère le tracking dans la BDD à partir du SKU

        $trackings = $trackingRepository->findBy([
            "SKU" => $SKU
        ], [
            "timestamp" => "ASC"
        ]);


        $lastTracking = end($trackings);

        // on convertit le tracking en un obj de class Stock
        $stock = Stock::getStockFromTracking($lastTracking);

        $dataArray = [$stock->getSKU(), $stock->getDescription(), $stock->getProductFamily(), $stock->getReference(), $stock->getPrice(), $stock->getSize1Name(), $stock->getSize1(), $stock->getSize1Unit(), $stock->getSize2Name(), $stock->getSize2(), $stock->getSize2Unit()];
        $dataToEncode = implode("\t", $dataArray);


        $qrCode = QrCode::create($dataToEncode)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300);

        // on encode la string
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Validate the result
        // Save it to a file
        // $result->saveToFile(__DIR__ . '/datamatrix-'. $id . '.png'); objectif !!
        $result->saveToFile(__DIR__ . '/../data-matrix/datamatrix-' . 'id' . '.png');

        $dataUri = $result->getDataUri();

        $dataUri = $result->getDataUri();


        return $this->render('read/index.html.twig', [
            'stock' => $stock,
            'dataUri' => $dataUri,
        ]);
    }
}
