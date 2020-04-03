<?php

namespace App\Repository;

use App\Entity\PartOfDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PartOfDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartOfDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartOfDay[]    findAll()
 * @method PartOfDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartOfDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartOfDay::class);
    }

    // /**
    //  * @return PartOfDay[] Returns an array of PartOfDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PartOfDay
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
