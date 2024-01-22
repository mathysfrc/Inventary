<?php

namespace App\Controller;

use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Stock;
use App\Entity\Tracking;
use App\Repository\TrackingRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConsommationController extends AbstractController
{

    #[Route('/consommation', name: 'app_consommation')]
    public function index(Request $request, TrackingRepository $trackingRepository, EntityManagerInterface $entityManager): Response
    {
        // On récupère le SKU à partir de la variable de notre requête POST
        $SKU = $request->request->get('SKU');
        
        // On récupère le tracking dans la BDD à partir du SKU
        $trackings = $trackingRepository->findBy([
            "SKU" => $SKU
        ], [
            "timestamp" => "ASC"
        ]);
        
        $lastTracking = end($trackings);
        
        // On convertit le tracking en un objet de la classe Stock
        $stock = Stock::getStockFromTracking($lastTracking);
        
        return $this->render('consommation/index.html.twig', [
            'stock' => $stock,
        ]);
    }
}