<?php

namespace App\Repository;

use App\Entity\PatientCredentials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PatientCredentials|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientCredentials|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientCredentials[]    findAll()
 * @method PatientCredentials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientCredentialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientCredentials::class);
    }

    // /**
    //  * @return PatientCredentials[] Returns an array of PatientCredentials objects
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
    public function findOneBySomeField($value): ?PatientCredentials
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
