<?php

namespace App\Repository;

use App\Entity\ActivityLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ActivityLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityLevel[]    findAll()
 * @method ActivityLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityLevelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivityLevel::class);
    }

//    /**
//     * @return ActivityLevel[] Returns an array of ActivityLevel objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActivityLevel
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
