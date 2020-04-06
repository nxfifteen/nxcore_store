<?php
/**
 * DONE This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */


namespace App\Repository;

use App\Entity\FitFloorsIntraDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitFloorsIntraDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitFloorsIntraDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitFloorsIntraDay[]    findAll()
 * @method FitFloorsIntraDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitFloorsIntraDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitFloorsIntraDay::class);
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function findByDateRange(String $patientId, String $date, int $trackingDevice = 0)
    {
        $today = $date . " 00:00:00";
        $todayEnd = $date . " 23:59:00";

        if ($trackingDevice > 0) {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.patient', 'p')
                ->andWhere('c.trackingDevice = :trackingDevice')
                ->setParameter('trackingDevice', $trackingDevice)
                ->andWhere('c.DateTime >= :val')
                ->setParameter('val', $today)
                ->andWhere('c.DateTime <= :valEnd')
                ->setParameter('valEnd', $todayEnd)
                ->andWhere('p.uuid = :patientId')
                ->setParameter('patientId', $patientId)
                ->orderBy('c.DateTime', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.patient', 'p')
                ->andWhere('c.DateTime >= :val')
                ->setParameter('val', $today)
                ->andWhere('c.DateTime <= :valEnd')
                ->setParameter('valEnd', $todayEnd)
                ->andWhere('p.uuid = :patientId')
                ->setParameter('patientId', $patientId)
                ->orderBy('c.DateTime', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }
}
