<?php

namespace App\Repository;

use App\Entity\SleepEpisode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SleepEpisode|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleepEpisode|null findOneBy(array $criteria, array $orderBy = null)
 * @method SleepEpisode[]    findAll()
 * @method SleepEpisode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SleepEpisodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SleepEpisode::class);
    }

//    /**
//     * @return SleepEpisode[] Returns an array of SleepEpisode objects
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
    public function findOneBySomeField($value): ?SleepEpisode
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
