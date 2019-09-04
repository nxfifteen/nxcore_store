<?php

namespace App\Repository;

use App\Entity\FitStepsIntraDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitStepsIntraDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitStepsIntraDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitStepsIntraDay[]    findAll()
 * @method FitStepsIntraDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitStepsIntraDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitStepsIntraDay::class);
    }

    // /**
    //  * @return FitFloorsIntraDay[] Returns an array of FitFloorsIntraDay objects
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
    public function findOneBySomeField($value): ?FitFloorsIntraDay
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
