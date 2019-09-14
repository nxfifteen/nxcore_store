<?php

namespace App\Repository;

use App\Entity\RpgRewardsAwarded;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgRewardsAwarded|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgRewardsAwarded|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgRewardsAwarded[]    findAll()
 * @method RpgRewardsAwarded[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgRewardsAwardedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgRewardsAwarded::class);
    }

    // /**
    //  * @return RpgRewardsAwarded[] Returns an array of RpgRewardsAwarded objects
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
    public function findOneBySomeField($value): ?RpgRewardsAwarded
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
