<?php

namespace App\Repository;

use App\Entity\FoodMeals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FoodMeals|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodMeals|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodMeals[]    findAll()
 * @method FoodMeals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodMealsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodMeals::class);
    }

    // /**
    //  * @return FoodMeals[] Returns an array of FoodMeals objects
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
    public function findOneBySomeField($value): ?FoodMeals
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
