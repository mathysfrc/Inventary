<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Form\SearchSkuType;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;


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
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stock/new.html.twig', [
            'stock' => $stock,
            'form' => $form,
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
            $entityManager->flush();

            return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/reference', name: 'app_stock_reference', methods: ['POST'])]
    public function reference(Request $request, Stock $stock, EntityManagerInterface $entityManager): Response
    {

        return $this->render('stock/reference.html.twig', [
            'stock' => $stock,

        ]);
    }
}
