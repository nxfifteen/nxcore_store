<?php

namespace App\Repository;

use App\Entity\SportTrackPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SportTrackPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportTrackPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportTrackPoint[]    findAll()
 * @method SportTrackPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportTrackPointRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SportTrackPoint::class);
    }

//    /**
//     * @return SportTrackPoint[] Returns an array of SportTrackPoint objects
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
    public function findOneBySomeField($value): ?SportTrackPoint
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
