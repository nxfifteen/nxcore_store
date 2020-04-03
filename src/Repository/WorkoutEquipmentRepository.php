<?php

namespace App\Repository;

use App\Entity\WorkoutEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WorkoutEquipment|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method WorkoutEquipment|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method WorkoutEquipment[]    findAll()
 * @method WorkoutEquipment[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class WorkoutEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkoutEquipment::class);
    }

    // /**
    //  * @return WorkoutEquipment[] Returns an array of WorkoutEquipment objects
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
    public function findOneBySomeField($value): ?WorkoutEquipment
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
