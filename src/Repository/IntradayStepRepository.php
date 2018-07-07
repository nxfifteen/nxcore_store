<?php

namespace App\Repository;

use App\Entity\IntradayStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IntradayStep|null find($id, $lockMode = null, $lockVersion = null)
 * @method IntradayStep|null findOneBy(array $criteria, array $orderBy = null)
 * @method IntradayStep[]    findAll()
 * @method IntradayStep[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntradayStepRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IntradayStep::class);
    }

//    /**
//     * @return IntradayStep[] Returns an array of IntradayStep objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IntradayStep
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
