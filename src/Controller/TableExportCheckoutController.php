<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\StockDataMatrix;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManagerInterface;

class TableExportCheckoutController extends AbstractController
{
    #[Route('/table/export/checkout', name: 'app_table_export_checkout')]
    public function exportTableCheckout(EntityManagerInterface $entityManager): Response
    
        {

            $repository = $entityManager->getRepository(StockDataMatrix::class);

            // Récupérez toutes les entrées de la table.
            $donnees = $repository->findAll();
        
            // Créez une chaîne de caractères pour stocker les données.
            $txtContent = "";
            // Récupérez les données de la table que vous souhaitez exporter (par exemple, à partir d'une entité Doctrine).
        
            // Formattez les données dans une chaîne de caractères txt.
            $txtContent = "SKU\tRéférence\tMois\tHeure\n"; 
            
            foreach ($donnees as $item) {
                $timezone = new DateTimeZone('Europe/Paris');
                $dateTimeString = $item->getDateTime()->setTimezone($timezone)->format('Y-m-d H:i:s');
                $txtContent .= $item->getSKU() . "\t" . $item->getReference() . "\t" . $item->getReferenceCheckout() .  "\t" . $dateTimeString . "\n";
            }
        
            // Créez une réponse Symfony avec le contenu du fichier txt.
            $response = new Response($txtContent);
        
            // Définissez les en-têtes pour indiquer le type de contenu et le téléchargement du fichier.
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'export.csv'
            );
        
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', 'text/plain');
        
            return $response;
        }
        
    }