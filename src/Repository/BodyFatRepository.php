<?php

namespace App\Repository;

use App\Entity\BodyFat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BodyFat|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyFat|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyFat[]    findAll()
 * @method BodyFat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyFatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyFat::class);
    }

    // /**
    //  * @return BodyFat[] Returns an array of BodyFat objects
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
    public function findOneBySomeField($value): ?BodyFat
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
