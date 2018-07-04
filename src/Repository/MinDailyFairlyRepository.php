<?php

namespace App\Repository;

use App\Entity\MinDailyFairly;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MinDailyFairly|null find($id, $lockMode = null, $lockVersion = null)
 * @method MinDailyFairly|null findOneBy(array $criteria, array $orderBy = null)
 * @method MinDailyFairly[]    findAll()
 * @method MinDailyFairly[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MinDailyFairlyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MinDailyFairly::class);
    }

//    /**
//     * @return MinDailyFairly[] Returns an array of MinDailyFairly objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MinDailyFairly
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
