<?php

namespace App\Repository;

use App\Entity\StepCountDaily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StepCountDaily|null find($id, $lockMode = null, $lockVersion = null)
 * @method StepCountDaily|null findOneBy(array $criteria, array $orderBy = null)
 * @method StepCountDaily[]    findAll()
 * @method StepCountDaily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StepCountDailyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StepCountDaily::class);
    }

//    /**
//     * @return StepCountDaily[] Returns an array of StepCountDaily objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StepCountDaily
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
