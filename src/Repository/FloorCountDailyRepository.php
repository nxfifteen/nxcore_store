<?php

namespace App\Repository;

use App\Entity\FloorCountDaily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FloorCountDaily|null find($id, $lockMode = null, $lockVersion = null)
 * @method FloorCountDaily|null findOneBy(array $criteria, array $orderBy = null)
 * @method FloorCountDaily[]    findAll()
 * @method FloorCountDaily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FloorCountDailyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FloorCountDaily::class);
    }

//    /**
//     * @return FloorCountDaily[] Returns an array of FloorCountDaily objects
//     */
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
    public function findOneBySomeField($value): ?FloorCountDaily
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
