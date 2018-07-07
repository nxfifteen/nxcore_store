<?php

namespace App\Repository;

use App\Entity\HeartRateOutOfRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeartRateOutOfRange|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeartRateOutOfRange|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeartRateOutOfRange[]    findAll()
 * @method HeartRateOutOfRange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRateOutOfRangeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeartRateOutOfRange::class);
    }

//    /**
//     * @return HeartRateOutOfRange[] Returns an array of HeartRateOutOfRange objects
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
    public function findOneBySomeField($value): ?HeartRateOutOfRange
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
