<?php

namespace App\Repository;

use App\Entity\ThirdPartyRelations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ThirdPartyRelations|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThirdPartyRelations|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThirdPartyRelations[]    findAll()
 * @method ThirdPartyRelations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThirdPartyRelationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ThirdPartyRelations::class);
    }

//    /**
//     * @return ThirdPartyRelations[] Returns an array of ThirdPartyRelations objects
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
    public function findOneBySomeField($value): ?ThirdPartyRelations
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
