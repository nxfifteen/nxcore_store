<?php

namespace App\Repository;

use App\Entity\CountDailyStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CountDailyStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountDailyStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountDailyStep[]    findAll()
 * @method CountDailyStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountDailyStepRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountDailyStep::class);
    }

//    /**
//     * @return CountDailyStep[] Returns an array of CountDailyStep objects
//     */
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
    public function findOneBySomeField($value): ?CountDailyStep
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
