<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function index(Request $request): Response
    {

        $error = $request->query->get('error');

        return $this->render('error/index.html.twig', [
            'controller_name' => 'ErrorController',
            'error' => $error,
        ]);
    }
}
