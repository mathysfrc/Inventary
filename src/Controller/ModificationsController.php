<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModificationsController extends AbstractController
{
    #[Route('/gestion', name: 'app_modifications')]
    public function index(): Response
    {
        return $this->render('modifications/index.html.twig', [
            'controller_name' => 'ModificationsController',
        ]);
    }
}
