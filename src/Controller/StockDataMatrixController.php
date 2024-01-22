<?php

namespace App\Controller;

use App\Entity\StockDataMatrix;
use App\Form\SearchSkuCheckoutFormType;
use App\Form\StockDataMatrix1Type;
use App\Repository\StockDataMatrixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/data/matrix')]
class StockDataMatrixController extends AbstractController
{
    #[Route('/', name: 'app_stock_data_matrix_index')]
    public function index(Request $request,StockDataMatrixRepository $stockDataMatrixRepository): Response
    {

        $form = $this->createForm(SearchSkuCheckoutFormType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $search = $form->getData()['search'];

            $stockDataMatrix = $stockDataMatrixRepository->findLikeName($search);
        } else {

            $stockDataMatrix = $stockDataMatrixRepository->findBy([], ['SKU' => 'ASC']);
        }

        return $this->render('stock_data_matrix/index.html.twig', [
            'stock_data_matrices' => $stockDataMatrix,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_stock_data_matrix_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stockDataMatrix = new StockDataMatrix();
        $form = $this->createForm(StockDataMatrix1Type::class, $stockDataMatrix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stockDataMatrix);
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_data_matrix_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock_data_matrix/new.html.twig', [
            'stock_data_matrix' => $stockDataMatrix,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_data_matrix_show', methods: ['GET'])]
    public function show(StockDataMatrix $stockDataMatrix): Response
    {
        return $this->render('stock_data_matrix/show.html.twig', [
            'stock_data_matrix' => $stockDataMatrix,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_data_matrix_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StockDataMatrix $stockDataMatrix, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StockDataMatrix1Type::class, $stockDataMatrix);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_data_matrix_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock_data_matrix/edit.html.twig', [
            'stock_data_matrix' => $stockDataMatrix,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_data_matrix_delete', methods: ['POST'])]
    public function delete(Request $request, StockDataMatrix $stockDataMatrix, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stockDataMatrix->getId(), $request->request->get('_token'))) {
            $entityManager->remove($stockDataMatrix);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stock_data_matrix_index', [], Response::HTTP_SEE_OTHER);
    }
}
