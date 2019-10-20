<?php

namespace App\Repository;

use App\Entity\RpgChallengeGlobal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgChallengeGlobal|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method RpgChallengeGlobal|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method RpgChallengeGlobal[]    findAll()
 * @method RpgChallengeGlobal[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class RpgChallengeGlobalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgChallengeGlobal::class);
    }

    // /**
    //  * @return RpgChallengeGlobal[] Returns an array of RpgChallengeGlobal objects
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
    public function findOneBySomeField($value): ?RpgChallengeGlobal
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
