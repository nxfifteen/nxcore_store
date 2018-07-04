<?php

namespace App\Repository;

use App\Entity\MinDailySedentary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MinDailySedentary|null find($id, $lockMode = null, $lockVersion = null)
 * @method MinDailySedentary|null findOneBy(array $criteria, array $orderBy = null)
 * @method MinDailySedentary[]    findAll()
 * @method MinDailySedentary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MinDailySedentaryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MinDailySedentary::class);
    }

//    /**
//     * @return MinDailySedentary[] Returns an array of MinDailySedentary objects
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
    public function findOneBySomeField($value): ?MinDailySedentary
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
