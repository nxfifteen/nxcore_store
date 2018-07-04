<?php

namespace App\Repository;

use App\Entity\CountDailyElevation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CountDailyElevation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountDailyElevation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountDailyElevation[]    findAll()
 * @method CountDailyElevation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountDailyElevationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountDailyElevation::class);
    }

//    /**
//     * @return CountDailyElevation[] Returns an array of CountDailyElevation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CountDailyElevation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
