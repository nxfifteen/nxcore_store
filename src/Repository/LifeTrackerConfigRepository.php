<?php

namespace App\Repository;

use App\Entity\LifeTrackerConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LifeTrackerConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method LifeTrackerConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method LifeTrackerConfig[]    findAll()
 * @method LifeTrackerConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LifeTrackerConfigRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LifeTrackerConfig::class);
    }

//    /**
//     * @return LifeTrackerConfig[] Returns an array of LifeTrackerConfig objects
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
    public function findOneBySomeField($value): ?LifeTrackerConfig
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
