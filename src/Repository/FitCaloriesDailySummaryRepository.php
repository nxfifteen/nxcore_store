<?php

namespace App\Repository;

use App\Entity\FitCaloriesDailySummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitCaloriesDailySummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitCaloriesDailySummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitCaloriesDailySummary[]    findAll()
 * @method FitCaloriesDailySummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitCaloriesDailySummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitCaloriesDailySummary::class);
    }

    // /**
    //  * @return FitCaloriesDailySummary[] Returns an array of FitCaloriesDailySummary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FitCaloriesDailySummary
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
