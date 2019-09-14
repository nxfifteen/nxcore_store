<?php

namespace App\Repository;

use App\Entity\RpgRewards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgRewards|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgRewards|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgRewards[]    findAll()
 * @method RpgRewards[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgRewardsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgRewards::class);
    }

    // /**
    //  * @return RpgRewards[] Returns an array of RpgRewards objects
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
    public function findOneBySomeField($value): ?RpgRewards
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
