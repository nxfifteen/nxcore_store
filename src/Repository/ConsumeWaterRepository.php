<?php

namespace App\Repository;

use App\Entity\ConsumeWater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ConsumeWater|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsumeWater|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsumeWater[]    findAll()
 * @method ConsumeWater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsumeWaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsumeWater::class);
    }

    // /**
    //  * @return ConsumeWater[] Returns an array of ConsumeWater objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConsumeWater
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
