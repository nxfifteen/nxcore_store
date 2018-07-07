<?php

namespace App\Repository;

use App\Entity\HeartRatePeak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeartRatePeak|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeartRatePeak|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeartRatePeak[]    findAll()
 * @method HeartRatePeak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRatePeakRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeartRatePeak::class);
    }

//    /**
//     * @return HeartRatePeak[] Returns an array of HeartRatePeak objects
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
    public function findOneBySomeField($value): ?HeartRatePeak
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
