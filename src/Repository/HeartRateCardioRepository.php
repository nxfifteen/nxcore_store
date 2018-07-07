<?php

namespace App\Repository;

use App\Entity\HeartRateCardio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeartRateCardio|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeartRateCardio|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeartRateCardio[]    findAll()
 * @method HeartRateCardio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRateCardioRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeartRateCardio::class);
    }

//    /**
//     * @return HeartRateCardio[] Returns an array of HeartRateCardio objects
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
    public function findOneBySomeField($value): ?HeartRateCardio
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
