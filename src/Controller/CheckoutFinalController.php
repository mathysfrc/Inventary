<?php

namespace App\Controller;

use App\Form\SearchSkuType;
use App\Repository\StockDataMatrixRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutFinalController extends AbstractController
{
    #[Route('/checkout/final', name: 'app_checkout_final')]
    public function checkoutDifference(Request $request, StockDataMatrixRepository $stockDataMatrixRepository, StockRepository $stockRepository): Response
    {
        $stock_data_matrices = $stockDataMatrixRepository->findAll();
        $stock = $stockRepository->findAll();

        return $this->render('checkout_final/index.html.twig', [
            'controller_name' => 'CheckoutFinalController',
            'stock_data_matrices' => $stock_data_matrices,
            'stocks' => $stock,
        ]);
    }

    #[Route('/checkout/final/stock', name: 'app_checkout_final_stock')]
    public function stockDifference(StockRepository $stockRepository): Response
    {
        $stock = $stockRepository->findAll();

        return $this->render('checkout_final/stock.html.twig', [
            'controller_name' => 'CheckoutFinalController',
            'stocks' => $stock,
        ]);
    }

    #[Route('/checkout/final/instructions', name: 'app_checkout_final_difference')]
    public function instructions(): Response
    {

        return $this->render('checkout_final/instruction.html.twig', [
            'controller_name' => 'CheckoutFinalController',
        ]);
    }

}
