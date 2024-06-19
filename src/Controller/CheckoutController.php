<?php

namespace App\Controller;

use App\Entity\Checkout;
use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Stock;

use Doctrine\ORM\EntityManagerInterface;

class CheckoutController extends AbstractController
{

    #[Route('/checkout', name: 'app_checkout')]
    public function index(Request $request, StockRepository $stockRepository, EntityManagerInterface $entityManager): Response
    {
        $jsonData = null;

        $SKU = $request->request->get('SKU');


        try {
            $jsonData = json_decode($request->getContent(), true );
            var_dump($request->getContent());exit();
        } catch(Exception $e) {
            return $this->redirectToRoute('app_error', ["error" => "Le paramètre de la requête est incorrect..."], Response::HTTP_SEE_OTHER);
        }

        var_dump($jsonData);exit();
    
        $newStock = new Checkout();
        $newStock->setSKU();
        $newStock->setDate(new \DateTime());

        $entityManager->persist($newStock);
        $entityManager->flush();

        
        return $this->render('checkout/index.html.twig');
    }
}