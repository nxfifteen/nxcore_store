<?php

namespace App\Repository;

use App\Entity\PersonalPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PersonalPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalPlan[]    findAll()
 * @method PersonalPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalPlanRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PersonalPlan::class);
    }

//    /**
//     * @return PersonalPlan[] Returns an array of PersonalPlan objects
//     */
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
    public function findOneBySomeField($value): ?PersonalPlan
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
