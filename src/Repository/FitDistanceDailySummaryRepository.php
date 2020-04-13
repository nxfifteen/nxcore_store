<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nx-health/store
 * @link      https://nxfifteen.me.uk/projects/nx-health/
 * @link      https://git.nxfifteen.rocks/nx-health/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

/** @noinspection DuplicatedCode */

namespace App\Repository;

use App\Entity\FitDistanceDailySummary;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * @method FitDistanceDailySummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitDistanceDailySummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitDistanceDailySummary[]    findAll()
 * @method FitDistanceDailySummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitDistanceDailySummaryRepository extends ServiceEntityRepository
{
    /**
     * FitDistanceDailySummaryRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitDistanceDailySummary::class);
    }

    /**
     * Find a Entity by its GUID
     *
     * @param string $value
     *
     * @return mixed
     */
    public function findByGuid(string $value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.guid = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param String $patientId
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function getSumOfValues(string $patientId, int $trackingDevice = 0)
    {
        if ($trackingDevice > 0) {
            try {
                return $this->createQueryBuilder('c')
                    ->leftJoin('c.patient', 'p')
                    ->andWhere('p.uuid = :patientId')
                    ->setParameter('patientId', $patientId)
                    ->andWhere('c.trackingDevice = :trackingDevice')
                    ->setParameter('trackingDevice', $trackingDevice)
                    ->select('sum(c.value) as sum')
                    ->getQuery()
                    ->getOneOrNullResult()['sum'];
            } catch (NonUniqueResultException $e) {
                return null;
            }
        } else {
            try {
                return $this->createQueryBuilder('c')
                    ->leftJoin('c.patient', 'p')
                    ->andWhere('p.uuid = :patientId')
                    ->setParameter('patientId', $patientId)
                    ->select('sum(c.value) as sum')
                    ->getQuery()
                    ->getOneOrNullResult()['sum'];
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }
    }

    /**
     * @param String $patientId
     * @param        $dateSince
     *
     * @return mixed
     */
    public function findSince(string $patientId, $dateSince)
    {
        /** @var DateTime $dateSince */
        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.uuid = :patientId')
            ->setParameter('patientId', $patientId)
            ->andWhere('c.DateTime >= :startDate')
            ->setParameter('startDate', $dateSince->format("Y-m-d 00:00:00"))
            ->orderBy('c.value', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param String $patientId
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function findHighest(string $patientId, int $trackingDevice = 0)
    {
        if ($trackingDevice > 0) {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.patient', 'p')
                ->andWhere('p.uuid = :patientId')
                ->setParameter('patientId', $patientId)
                ->andWhere('c.trackingDevice = :trackingDevice')
                ->setParameter('trackingDevice', $trackingDevice)
                ->orderBy('c.value', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('c')
                ->leftJoin('c.patient', 'p')
                ->andWhere('p.uuid = :patientId')
                ->setParameter('patientId', $patientId)
                ->orderBy('c.value', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $trackingDevice
     *
     * @return mixed
     * @throws Exception
     */
    public function findByDateRange(string $patientId, string $date, int $trackingDevice)
    {
        return $this->findByDateRangeHistorical($patientId, $date, 0, $trackingDevice);
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $lastDays
     * @param int    $trackingDevice
     *
     * @return mixed
     * @throws Exception
     */
    public function findByDateRangeHistorical(string $patientId, string $date, int $lastDays, int $trackingDevice = 0)
    {
        $dateObject = new DateTime($date);

        try {
            $interval = new DateInterval('P' . $lastDays . 'D');
            $dateObject->sub($interval);
            $today = $dateObject->format("Y-m-d") . " 00:00:00";
        } catch (Exception $e) {
            $today = $date . " 00:00:00";
        }
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
