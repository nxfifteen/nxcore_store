<?php

namespace App\Repository;

use App\Entity\TrackingDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TrackingDevice|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackingDevice|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackingDevice[]    findAll()
 * @method TrackingDevice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingDeviceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TrackingDevice::class);
    }

    public function findOneByRemoteId($value): ?TrackingDevice
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.remote_id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
