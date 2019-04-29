<?php

namespace App\Repository;

use App\Entity\UnitOfMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UnitOfMeasurement|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitOfMeasurement|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitOfMeasurement[]    findAll()
 * @method UnitOfMeasurement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitOfMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UnitOfMeasurement::class);
    }

    public function findOneByName($value): ?UnitOfMeasurement
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    /**
//     * @return UnitOfMeasurement[] Returns an array of UnitOfMeasurement objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*

    */
}
