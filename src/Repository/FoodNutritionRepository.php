<?php

namespace App\Repository;

use App\Entity\FoodNutrition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FoodNutrition|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodNutrition|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodNutrition[]    findAll()
 * @method FoodNutrition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodNutritionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodNutrition::class);
    }

    // /**
    //  * @return FoodNutrition[] Returns an array of FoodNutrition objects
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
    public function findOneBySomeField($value): ?FoodNutrition
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
