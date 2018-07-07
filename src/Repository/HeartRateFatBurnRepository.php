<?php

namespace App\Repository;

use App\Entity\HeartRateFatBurn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HeartRateFatBurn|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeartRateFatBurn|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeartRateFatBurn[]    findAll()
 * @method HeartRateFatBurn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeartRateFatBurnRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HeartRateFatBurn::class);
    }

//    /**
//     * @return HeartRateFatBurn[] Returns an array of HeartRateFatBurn objects
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
    public function findOneBySomeField($value): ?HeartRateFatBurn
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
