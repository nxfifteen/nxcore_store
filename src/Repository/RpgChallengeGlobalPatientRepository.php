<?php

namespace App\Repository;

use App\Entity\RpgChallengeGlobalPatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgChallengeGlobalPatient|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method RpgChallengeGlobalPatient|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method RpgChallengeGlobalPatient[]    findAll()
 * @method RpgChallengeGlobalPatient[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class RpgChallengeGlobalPatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgChallengeGlobalPatient::class);
    }

    // /**
    //  * @return RpgChallengeGlobalPatient[] Returns an array of RpgChallengeGlobalPatient objects
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
    public function findOneBySomeField($value): ?RpgChallengeGlobalPatient
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
