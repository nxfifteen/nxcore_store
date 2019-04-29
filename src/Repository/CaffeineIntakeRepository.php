<?php

namespace App\Repository;

use App\Entity\CaffeineIntake;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CaffeineIntake|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaffeineIntake|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaffeineIntake[]    findAll()
 * @method CaffeineIntake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaffeineIntakeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CaffeineIntake::class);
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
