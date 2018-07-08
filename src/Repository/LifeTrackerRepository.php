<?php

namespace App\Repository;

use App\Entity\LifeTracker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LifeTracker|null find($id, $lockMode = null, $lockVersion = null)
 * @method LifeTracker|null findOneBy(array $criteria, array $orderBy = null)
 * @method LifeTracker[]    findAll()
 * @method LifeTracker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LifeTrackerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LifeTracker::class);
    }

//    /**
//     * @return LifeTracker[] Returns an array of LifeTracker objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LifeTracker
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
