<?php

namespace App\Repository;

use App\Entity\MinDailyLightly;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MinDailyLightly|null find($id, $lockMode = null, $lockVersion = null)
 * @method MinDailyLightly|null findOneBy(array $criteria, array $orderBy = null)
 * @method MinDailyLightly[]    findAll()
 * @method MinDailyLightly[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MinDailyLightlyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MinDailyLightly::class);
    }

//    /**
//     * @return MinDailyLightly[] Returns an array of MinDailyLightly objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MinDailyLightly
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
