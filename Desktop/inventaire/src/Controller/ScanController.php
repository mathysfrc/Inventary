<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
