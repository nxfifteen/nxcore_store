<?php

namespace App\Repository;

use App\Entity\CountDailyDistance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CountDailyDistance|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountDailyDistance|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountDailyDistance[]    findAll()
 * @method CountDailyDistance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountDailyDistanceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountDailyDistance::class);
    }

//    /**
//     * @return CountDailyDistance[] Returns an array of CountDailyDistance objects
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
    public function findOneBySomeField($value): ?CountDailyDistance
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
