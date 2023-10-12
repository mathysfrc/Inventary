<?php

namespace App\Controller;

use App\Entity\Tracking;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\EntityManagerInterface;

class TableExportV2Controller extends AbstractController
{
    #[Route('/table/export', name: 'app_table_export')]
    public function exportTable(EntityManagerInterface $entityManager): Response
    
        {

            $repository = $entityManager->getRepository(Tracking::class);

            // Récupérez toutes les entrées de la table.
            $donnees = $repository->findAll();
        
            // Créez une chaîne de caractères pour stocker les données.
            $txtContent = "";
            // Récupérez les données de la table que vous souhaitez exporter (par exemple, à partir d'une entité Doctrine).
        
            // Formattez les données dans une chaîne de caractères txt.
            $txtContent = "SKU\tDescription\tDimension\tRésultat\tPrix\tFamille produit\tRéférence\tType de mouvement\tDate\tStatus\n"; 
            
            foreach ($donnees as $item) {
                $txtContent .= $item->getSKU() . "\t" . $item->getDescription() . "\t" . $item->getSize1Name() . ' : ' . $item->getSize1() . ' ' .  $item->getSize1Unit() . ' x '  . $item->getSize2Name() . ' : ' . $item->getSize2() . ' ' . $item->getSize2Unit()  . "\t";
            
                if ($item->getSize1Unit() == "mm"  && $item->getSize2Unit() == "m") {
                    $txtContent .= ($item->getSize1() * $item->getSize2()) / 1000 . ' m² ';
                } elseif ($item->getSize1Unit() == 'unité'  && $item->getSize2Unit() == 'L') {
                    $txtContent .= ($item->getSize1() * $item->getSize2()) . ' L ';
                }
            
                $txtContent .= "\t" . $item->getPrice() .  ' € '  .  "\t" . $item->getProductFamily() .  "\t" . $item->getReference() . "\t" . $item->getMovementType() . "\t" . $item->getTimestamp()->format('d-m-Y H:i:s') . "\t" . $item->getStatus() . "\n";
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
