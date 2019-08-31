<?php

namespace App\Repository;

use App\Entity\FitStepsDailySummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitStepsDailySummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitStepsDailySummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitStepsDailySummary[]    findAll()
 * @method FitStepsDailySummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitStepsDailySummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitStepsDailySummary::class);
    }

    // /**
    //  * @return FitStepsDailySummary[] Returns an array of FitStepsDailySummary objects
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
    public function findOneBySomeField($value): ?FitStepsDailySummary
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
