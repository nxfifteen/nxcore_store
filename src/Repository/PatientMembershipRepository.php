<?php

namespace App\Repository;

use App\Entity\PatientMembership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PatientMembership|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientMembership|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientMembership[]    findAll()
 * @method PatientMembership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientMembership::class);
    }

    // /**
    //  * @return PatientMembership[] Returns an array of PatientMembership objects
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
    public function findOneBySomeField($value): ?PatientMembership
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
