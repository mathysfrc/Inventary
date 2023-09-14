<?php

namespace App\Controller;

use App\Entity\Tracking;
use App\Form\TrackingType;
use App\Repository\StockRepository;
use App\Repository\TrackingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tracking')]
class TrackingController extends AbstractController
{
    #[Route('/', name: 'app_tracking_index', methods: ['GET'])]
    public function index(TrackingRepository $trackingRepository, Request $request, StockRepository $stockRepository): Response
    {
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

    #[Route('/new', name: 'app_tracking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tracking = new Tracking();
        $form = $this->createForm(TrackingType::class, $tracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tracking);
            $entityManager->flush();

            return $this->redirectToRoute('app_tracking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tracking/new.html.twig', [
            'tracking' => $tracking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tracking_show', methods: ['GET'])]
    public function show(Tracking $tracking): Response
    {
        return $this->render('tracking/show.html.twig', [
            'tracking' => $tracking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tracking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tracking $tracking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrackingType::class, $tracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tracking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tracking/edit.html.twig', [
            'tracking' => $tracking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tracking_delete', methods: ['POST'])]
    public function delete(Request $request, Tracking $tracking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tracking->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tracking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tracking_index', [], Response::HTTP_SEE_OTHER);
    }
}
