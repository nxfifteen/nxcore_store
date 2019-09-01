<?php

namespace App\Repository;

use App\Entity\ExerciseSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ExerciseSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExerciseSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExerciseSummary[]    findAll()
 * @method ExerciseSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseSummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseSummary::class);
    }

    // /**
    //  * @return ExerciseSummary[] Returns an array of ExerciseSummary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExerciseSummary
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
