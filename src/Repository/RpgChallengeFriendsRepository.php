<?php

namespace App\Repository;

use App\Entity\RpgChallengeFriends;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgChallengeFriends|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgChallengeFriends|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgChallengeFriends[]    findAll()
 * @method RpgChallengeFriends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgChallengeFriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgChallengeFriends::class);
    }

    // /**
    //  * @return RpgChallengeFriends[] Returns an array of RpgChallengeFriends objects
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
    public function findOneBySomeField($value): ?RpgChallengeFriends
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
