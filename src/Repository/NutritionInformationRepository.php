<?php

namespace App\Repository;

use App\Entity\NutritionInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NutritionInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method NutritionInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method NutritionInformation[]    findAll()
 * @method NutritionInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NutritionInformationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NutritionInformation::class);
    }

//    /**
//     * @return NutritionInformation[] Returns an array of NutritionInformation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NutritionInformation
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
