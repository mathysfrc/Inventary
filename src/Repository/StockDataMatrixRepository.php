<?php

namespace App\Repository;

use App\Entity\StockDataMatrix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockDataMatrix>
 *
 * @method StockDataMatrix|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockDataMatrix|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockDataMatrix[]    findAll()
 * @method StockDataMatrix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockDataMatrixRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockDataMatrix::class);
    }

    public function findLikeName(string $name)

{

    $queryBuilder = $this->createQueryBuilder('p')

        ->where('p.SKU LIKE :name')

        ->setParameter('name', '%' . $name . '%')

        ->getQuery();


    return $queryBuilder->getResult();

}

//    /**
//     * @return StockDataMatrix[] Returns an array of StockDataMatrix objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StockDataMatrix
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
