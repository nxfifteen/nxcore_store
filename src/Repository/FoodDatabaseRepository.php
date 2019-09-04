<?php

namespace App\Repository;

use App\Entity\FoodDatabase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method FoodDatabase|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method FoodDatabase|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method FoodDatabase[]    findAll()
 * @method FoodDatabase[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class FoodDatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodDatabase::class);
    }

    /**
     * @param $food_info_id
     *
     * @return FoodDatabase|null Returns an array of FoodDatabase objects
     */
    public function findByFoodInfoId($food_info_id): ?FoodDatabase
    {
        try {
            return $this->createQueryBuilder('f')
                ->andWhere('f.remoteIds LIKE :val')
                ->setParameter('val', "%" . addcslashes($food_info_id, "%_") . "%")
                ->orderBy('f.id', 'ASC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return NULL;
        }
    }
}
