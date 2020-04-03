<?php

namespace App\Repository;

use App\Entity\FoodDiary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FoodDiary|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodDiary|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodDiary[]    findAll()
 * @method FoodDiary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodDiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodDiary::class);
    }

    // /**
    //  * @return FoodDiary[] Returns an array of FoodDiary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FoodDiary
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
