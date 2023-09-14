<?php

namespace App\Controller;

use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\StockController;
use App\Entity\Tracking;

class ConsommationController extends AbstractController
{

    #[Route('/consommation', name: 'app_consommation')]
    public function index(Request $request, StockRepository $stockRepository): Response
    {
        $form = $this->createForm(SearchSkuType::class);

        $form->handleRequest($request);
    
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            $search = $form->getData()['search'];
        
            $sku = $stockRepository->findLikeName($search);
        
            return $this->render('consommation/index.html.twig', [
                'stocks' => $sku,
                'form' => $form,
            ]);
        } 
        
        return $this->render('consommation/index.html.twig', [
        'form' => $form,
        'stocks' => '',
        ]);
    }
}















