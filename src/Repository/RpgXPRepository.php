<?php

namespace App\Repository;

use App\Entity\RpgXP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgXP|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgXP|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgXP[]    findAll()
 * @method RpgXP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgXPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgXP::class);
    }

    // /**
    //  * @return RpgXP[] Returns an array of RpgXP objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RpgXP
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
