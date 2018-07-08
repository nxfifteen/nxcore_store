<?php

namespace App\Repository;

use App\Entity\LifeTracked;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LifeTracked|null find($id, $lockMode = null, $lockVersion = null)
 * @method LifeTracked|null findOneBy(array $criteria, array $orderBy = null)
 * @method LifeTracked[]    findAll()
 * @method LifeTracked[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LifeTrackedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LifeTracked::class);
    }

//    /**
//     * @return LifeTracked[] Returns an array of LifeTracked objects
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
    public function findOneBySomeField($value): ?LifeTracked
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
