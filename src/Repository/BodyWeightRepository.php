<?php

namespace App\Repository;

use App\Entity\BodyWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BodyWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyWeight[]    findAll()
 * @method BodyWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyWeightRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BodyWeight::class);
    }

//    /**
//     * @return BodyWeight[] Returns an array of BodyWeight objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BodyWeight
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
