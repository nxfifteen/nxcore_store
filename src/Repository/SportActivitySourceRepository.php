<?php

namespace App\Repository;

use App\Entity\SportActivitySource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SportActivitySource|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportActivitySource|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportActivitySource[]    findAll()
 * @method SportActivitySource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportActivitySourceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SportActivitySource::class);
    }

//    /**
//     * @return SportActivitySource[] Returns an array of SportActivitySource objects
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
    public function findOneBySomeField($value): ?SportActivitySource
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
