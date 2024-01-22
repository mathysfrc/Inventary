<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoaderController extends AbstractController
{
    #[Route('/loader', name: 'app_loader')]
    public function index(): Response
    {
        return $this->render('loader/index.html.twig', [
            'controller_name' => 'LoaderController',
        ]);
    }
}
