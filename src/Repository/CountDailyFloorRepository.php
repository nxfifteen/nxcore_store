<?php

namespace App\Repository;

use App\Entity\CountDailyFloor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CountDailyFloor|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountDailyFloor|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountDailyFloor[]    findAll()
 * @method CountDailyFloor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountDailyFloorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountDailyFloor::class);
    }

//    /**
//     * @return CountDailyFloor[] Returns an array of CountDailyFloor objects
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
    public function findOneBySomeField($value): ?CountDailyFloor
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
