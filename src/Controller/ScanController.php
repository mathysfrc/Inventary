<?php

namespace App\Controller;

use App\Entity\StockDataMatrix;
use App\Form\StockDataMatrixType;
use App\Repository\StockDataMatrixRepository;
use DateTime;
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

        // On récupère dans le param GET de l'url la référence qu'il faut afficher. SI jamais on ne l'a pas => array vide
        $session = $request->getSession();
        $ref = $request->query->get('ref') ?? $session->get('reference_checkout'); // On récupère le paramètre GET (...?ref=février+2024) OU la valeur stockée dans la session
        $stockDataMatrixs = [];
        if ($ref) {
            $session->set('reference_checkout', $ref); // Ici, on rentre la valeur dans la session
            $stockDataMatrixs = $stockDataMatrixRepository->findBy(['reference_checkout' => $ref]);
        }
        // On récupère toutes les différentes références de la table.
        $conn = $entityManager->getConnection();
        $sql = 'SELECT DISTINCT reference_checkout FROM stock_data_matrix';
        $resultSet = $conn->executeQuery($sql, ['ref' => $ref]);
        $reference_checkouts = $resultSet->fetchFirstColumn();

        // Formulaire quand une entité est scannée et que l'on veut la rajouter dans la table avec sa réf.
        if ($form->isSubmitted() && $form->isValid()) {
            $stockDataMatrix = $form->getData();
    
            // Remplacer les '§' par des '-' dans le champ pertinent
            $description = $stockDataMatrix->getDescription();
            $description = str_replace('§', '-', $description);
            $stockDataMatrix->setDescription($description);

            $reference = $stockDataMatrix->getReference();
            $reference = str_replace('§', '-', $reference);
            $stockDataMatrix->setReference($reference);
    
            // Vérifier si le SKU a déjà été scanné
            $newSKU = $stockDataMatrix->getSKU();
            foreach ($stockDataMatrixs as $existingDataMatrix) {
                if ($existingDataMatrix->getSKU() === $newSKU) {
                    $error = 'Ce SKU a déjà été scanné';
                }
            }
            if (!$error) {
                setlocale(LC_TIME, 'en_US'); // Définir la localisation en anglais
                $currentDateTime = new DateTime(); // Crée un nouvel objet DateTime pour l'heure actuelle
                $currentTimestamp = $currentDateTime->getTimestamp(); // Obtenez le timestamp UNIX actuel
                $formattedMonth = ucfirst(strftime('%B %Y', $currentTimestamp)); // Utiliser le timestamp pour obtenir le mois formaté avec la première lettre en majuscule
                $stockDataMatrix->setReferenceCheckout($formattedMonth);
                $stockDataMatrix->setDateTime($currentDateTime); // Passer l'objet DateTime
                
            
                

    
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
            'reference_checkouts' => $reference_checkouts,
            'active_ref' => $ref,
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