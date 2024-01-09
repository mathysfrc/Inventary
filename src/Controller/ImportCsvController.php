<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use League\Csv\Reader;
class ImportCsvController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/import/csv', name: 'app_import_csv')]
    public function importCsv(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $csvFile = $request->files->get('csv_file');

            if ($csvFile) {
                $csvData = file_get_contents($csvFile->getPathname());
                $csvData = mb_convert_encoding($csvData, 'UTF-8', 'UTF-8');
                $rows = explode("\n", trim($csvData));

                foreach ($rows as $row) {
                    $columns = str_getcsv($row, ';'); // Adapt this delimiter based on your CSV file

                    $entity = new Stock();
                    $entity->setSKU($columns[0]);
                    $entity->setDescription($columns[1]);
                    $entity->setSize1Name($columns[2]);
                    $entity->setSize1(floatval($columns[3]));
                    $entity->setSize1Unit($columns[4]);
                    $entity->setSize2Name($columns[5]);
                    $entity->setSize2(floatval($columns[6]));
                    $entity->setSize2Unit($columns[7]);
                    $entity->setSurface($columns[8]);
                    $entity->setPrice(floatval($columns[9]));
                    $entity->setProductFamily($columns[10]);
                    $entity->setReference($columns[11]);
                    $entity->setStatus($columns[12]);
                    $entity->setShape($columns[13]);

                    // Add other properties as needed

                    $this->entityManager->persist($entity);
                }

                $this->entityManager->flush();

                $this->addFlash('success', 'Importation réussie.');
            } else {
                $this->addFlash('error', 'Fichier CSV non trouvé.');
            }
        }

        return $this->render('import_csv/index.html.twig');
    }
}
