<?php

namespace App\Repository;

use App\Entity\FitFloorsIntraDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitFloorsIntraDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitFloorsIntraDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitFloorsIntraDay[]    findAll()
 * @method FitFloorsIntraDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitFloorsIntraDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitFloorsIntraDay::class);
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
