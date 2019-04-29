<?php

namespace App\Repository;

use App\Entity\WaterIntake;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WaterIntake|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaterIntake|null findOneBy(array $criteria, array $orderBy = null)
 * @method WaterIntake[]    findAll()
 * @method WaterIntake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaterIntakeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WaterIntake::class);
    }

    public function findByDateRange( String $patientId, String $date ) {
        $today = $date . " 00:00:00";
        $todayEnd = $date . " 23:59:00";

        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('c.date_time >= :val')
            ->setParameter('val', $today)
            ->andWhere('c.date_time <= :valEnd')
            ->setParameter('valEnd', $todayEnd)
            ->andWhere('p.uuid = :patientId')
            ->setParameter('patientId', $patientId)
            ->orderBy('c.date_time', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
