<?php

namespace App\Repository;

use App\Entity\PatientFriends;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PatientFriends|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientFriends|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientFriends[]    findAll()
 * @method PatientFriends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientFriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientFriends::class);
    }

    // /**
    //  * @return PatientFriends[] Returns an array of PatientFriends objects
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
    public function findOneBySomeField($value): ?PatientFriends
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
