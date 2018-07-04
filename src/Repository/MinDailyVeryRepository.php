<?php

namespace App\Repository;

use App\Entity\MinDailyVery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MinDailyVery|null find($id, $lockMode = null, $lockVersion = null)
 * @method MinDailyVery|null findOneBy(array $criteria, array $orderBy = null)
 * @method MinDailyVery[]    findAll()
 * @method MinDailyVery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MinDailyVeryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MinDailyVery::class);
    }

//    /**
//     * @return MinDailyVery[] Returns an array of MinDailyVery objects
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
    public function findOneBySomeField($value): ?MinDailyVery
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
