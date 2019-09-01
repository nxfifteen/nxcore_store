<?php

namespace App\Repository;

use App\Entity\BodyWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BodyWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyWeight[]    findAll()
 * @method BodyWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyWeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyWeight::class);
    }

    // /**
    //  * @return BodyWeight[] Returns an array of BodyWeight objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BodyWeight
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
