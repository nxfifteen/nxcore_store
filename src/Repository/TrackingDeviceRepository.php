<?php

namespace App\Repository;

use App\Entity\TrackingDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TrackingDevice|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackingDevice|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackingDevice[]    findAll()
 * @method TrackingDevice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingDeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackingDevice::class);
    }

    // /**
    //  * @return TrackingDevice[] Returns an array of TrackingDevice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrackingDevice
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
