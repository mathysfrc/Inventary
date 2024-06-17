<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Tracking;
use App\Repository\StockRepository;
use App\Repository\TrackingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsommationEmptyController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/consommation/empty', name: 'app_consommation_empty')]
    public function index(Request $request, TrackingRepository $trackingRepository, EntityManagerInterface $entityManager): Response
    {
        //on récupère le SKU a partir de la variable de notre requête POST
        $SKU = $request->request->get('SKU');

        $produitDejaConsume = $this->checkIfProductAlreadyConsumed($SKU);

        if ($produitDejaConsume) {
            $this->addFlash('error', 'Le produit a déjà été consommé.');
        }


        // on recupère le tracking dans la BDD à partir du SKU

        $trackings = $trackingRepository->findBy([
            "SKU" => $SKU
        ], [
            "timestamp" => "ASC"
        ]);

        $lastTracking = end($trackings);
        // on convertit le tracking en un obj de class Stock

        $stock = Stock::getStockFromTracking($lastTracking);



        return $this->render('consommation_empty/index.html.twig', [
            'stock' => $stock,
            'produitDejaConsume' => $produitDejaConsume,

        ]);
    }

    private function checkIfProductAlreadyConsumed($SKU)
    {
        $productRepository = $this->entityManager->getRepository(Stock::class);
        $product = $productRepository->findOneBy(['SKU' => $SKU]);

        return $product && $product->isConsumed();
    }

    #[Route('/empty/template', name: 'app_template_empty', methods: ["POST"])]
    public function empty(Request $request, TrackingRepository $trackingRepository, StockRepository $stockRepository, EntityManagerInterface $entityManager): Response
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
        // on récupère le stock dans la bdd

        $stock = $stockRepository->findOneBy([
            'SKU' => $SKU
        ]);
        // on va chercher a rajouter la ligne dans le tracking

        $newTracking = new Tracking();
        $newTracking->setSKU($lastTracking->getSKU());
        $newTracking->setDescription($lastTracking->getDescription());
        $newTracking->setSize1($lastTracking->getSize1());
        $newTracking->setSize2($lastTracking->getSize2());
        $newTracking->setSize1Unit($lastTracking->getSize1Unit());
        $newTracking->setSize2Unit($lastTracking->getSize2Unit());
        $newTracking->setSize1Name($lastTracking->getSize1Name());
        $newTracking->setSize2Name($lastTracking->getSize2Name());
        $newTracking->setPrice($lastTracking->getPrice());
        $newTracking->setProductFamily($lastTracking->getProductFamily());
        $newTracking->setReference($lastTracking->getReference());
        $newTracking->setFree($lastTracking->getFree());
        $newTracking->setComment($lastTracking->getComment());
        $newTracking->setSurface($lastTracking->getSurface());
        $newTracking->setShape($lastTracking->getShape());
        $newTracking->setStatus($lastTracking->getStatus());
        $newTracking->setMovementType("Vide");
        $newTracking->setTimestamp(new DateTime());

        $entityManager->persist($newTracking);

        // on supprime la ligne de "Stock"

        $entityManager->remove($stock);

        $entityManager->flush();


        return $this->render('empty_template/index.html.twig', [
            'stock' => $stock,
        ]);
    }
}