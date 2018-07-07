<?php

namespace App\Repository;

use App\Entity\SportTrack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SportTrack|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportTrack|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportTrack[]    findAll()
 * @method SportTrack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportTrackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SportTrack::class);
    }

//    /**
//     * @return SportTrack[] Returns an array of SportTrack objects
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
    public function findOneBySomeField($value): ?SportTrack
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
