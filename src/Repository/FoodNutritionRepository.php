<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

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
    /**
     * FoodNutritionRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodNutrition::class);
    }

    /**
     * Find a Entity by its GUID
     *
     * @param string $value
     *
     * @return mixed
     */
    public function findByGuid(string $value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.guid = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
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
