<?php

namespace App\Controller;

use App\Entity\StockDataMatrix;
use App\Form\StockDataMatrixType;
use App\Repository\StockDataMatrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScanController extends AbstractController
{
    #[Route('/scan/entry', name: 'app_scan_entry')]
    public function index(): Response
    {
        return $this->render('scan/index.html.twig', [
            'controller_name' => 'ScanController',
        ]);
    }

    #[Route('/scan/exit', name: 'app_scan_exit')]
    public function exit(): Response
    {
        return $this->render('scan/index.html.twig', [
            'controller_name' => 'ScanController',
        ]);
    }

    #[Route('/scan/empty', name: 'app_scan_empty')]
    public function empty(): Response
    {
        return $this->render('scan/empty.html.twig', [
            'controller_name' => 'ScanController',
        ]);
    }

    #[Route('/scan/read', name: 'app_scan_read')]
    public function read(): Response
    {
        return $this->render('scan/read.html.twig', [
            'controller_name' => 'ScanController',
        ]);
    }

    #[Route('/scan/checkout', name: 'app_scan_checkout')]
    public function checkout(Request $request, StockDataMatrixRepository $stockDataMatrixRepository, EntityManagerInterface $entityManager, String $error = null): Response
    {
        $stockDataMatrix = new StockDataMatrix();

        $form = $this->createForm(StockDataMatrixType::class, $stockDataMatrix);

        $form->handleRequest($request);

        $stockDataMatrixs = $stockDataMatrixRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $stockDataMatrix = $form->getData();
            // Vérifier si le SKU a déjà été scanné
            $newSKU = $stockDataMatrix->getSKU();
            foreach ($stockDataMatrixs as $exstingDataMatrix) {
                if ( $exstingDataMatrix->getSKU() === $newSKU ) {
                    $error = 'Ce SKU a déjà été scanné';
                }
            }
            if (!$error ) {
                $entityManager->persist($stockDataMatrix);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_scan_checkout', [
                'error' => $error,
            ]);
        } 
                
        return $this->render('scan/checkout.html.twig', [
            'controller_name' => 'ScanController',
            'stockDataMatrixs' => $stockDataMatrixs,
            'form' => $form,
            'error' => $error,
            
        ]);
    }


    #[Route('/scan/checkout/deleteAll', name: 'app_scan_checkout_deleteAll')]
    public function deleteAll(Request $request, StockDataMatrixRepository $stockDataMatrixRepository, EntityManagerInterface $entityManager): Response
    {
        $stockDataMatrix = new StockDataMatrix();

        $form = $this->createForm(StockDataMatrixType::class, $stockDataMatrix);

        $form->handleRequest($request);

        $stockDataMatrixs = $stockDataMatrixRepository->findAll();

        foreach ( $stockDataMatrixs as $dataMatrix ) {
            $entityManager->remove($dataMatrix);
        }

        $entityManager->flush();
                
        return $this->redirectToRoute('app_scan_checkout');
    }

    #[Route('/scan/checkout/delete/{id}', name: 'app_scan_checkout_delete')]
    public function delete($id, StockDataMatrixRepository $stockDataMatrixRepository, EntityManagerInterface $entityManager): Response
    {
        $stockDataMatrix = $stockDataMatrixRepository->find($id);
    
        $entityManager->remove($stockDataMatrix);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_scan_checkout');
    }
}