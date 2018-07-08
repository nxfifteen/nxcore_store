<?php

namespace App\Repository;

use App\Entity\HeartRateResting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeartRateResting|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeartRateResting|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeartRateResting[]    findAll()
 * @method HeartRateResting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRateRestingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeartRateResting::class);
    }

//    /**
//     * @return HeartRateResting[] Returns an array of HeartRateResting objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HeartRateResting
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
