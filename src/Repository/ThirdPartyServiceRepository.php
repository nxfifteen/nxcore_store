<?php

namespace App\Repository;

use App\Entity\ThirdPartyService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ThirdPartyService|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThirdPartyService|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThirdPartyService[]    findAll()
 * @method ThirdPartyService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThirdPartyServiceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ThirdPartyService::class);
    }

//    /**
//     * @return ThirdPartyService[] Returns an array of ThirdPartyService objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ThirdPartyService
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
