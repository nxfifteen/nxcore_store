<?php

namespace App\Repository;

use App\Entity\RpgIndicator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgIndicator|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgIndicator|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgIndicator[]    findAll()
 * @method RpgIndicator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgIndicatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgIndicator::class);
    }

    // /**
    //  * @return RpgIndicator[] Returns an array of RpgIndicator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RpgIndicator
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
