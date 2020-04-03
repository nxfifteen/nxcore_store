<?php

namespace App\Repository;

use App\Entity\PatientGoals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PatientGoals|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientGoals|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientGoals[]    findAll()
 * @method PatientGoals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientGoalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientGoals::class);
    }

    // /**
    //  * @return PatientGoals[] Returns an array of PatientGoals objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PatientGoals
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
