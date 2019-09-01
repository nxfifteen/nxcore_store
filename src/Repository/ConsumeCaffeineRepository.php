<?php

namespace App\Repository;

use App\Entity\ConsumeCaffeine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ConsumeCaffeine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsumeCaffeine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsumeCaffeine[]    findAll()
 * @method ConsumeCaffeine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsumeCaffeineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsumeCaffeine::class);
    }

    // /**
    //  * @return ConsumeCaffeine[] Returns an array of ConsumeCaffeine objects
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
    public function findOneBySomeField($value): ?ConsumeCaffeine
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
