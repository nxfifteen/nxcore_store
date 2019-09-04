<?php

namespace App\Repository;

use App\Entity\BodyComposition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BodyComposition|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyComposition|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyComposition[]    findAll()
 * @method BodyComposition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyCompositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyComposition::class);
    }

    // /**
    //  * @return BodyComposition[] Returns an array of BodyComposition objects
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
    public function findOneBySomeField($value): ?BodyComposition
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
