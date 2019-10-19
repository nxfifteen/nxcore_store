<?php

namespace App\Repository;

use App\Entity\FitDistanceDailySummary;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method FitDistanceDailySummary|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method FitDistanceDailySummary|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method FitDistanceDailySummary[]    findAll()
 * @method FitDistanceDailySummary[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class FitDistanceDailySummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitDistanceDailySummary::class);
    }

    /**
     * @param String $patientId
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function getSumOfValues(String $patientId, int $trackingDevice = 0)
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
                return NULL;
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
                return NULL;
            }
        }
    }

    public function findSince(String $patientId, $dateSince)
    {
        /** @var \DateTime $dateSince */
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

    public function findHighest(String $patientId, int $trackingDevice = 0)
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
     */
    public function findByDateRange(String $patientId, String $date, int $trackingDevice)
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
     */
    public function findByDateRangeHistorical(String $patientId, String $date, int $lastDays, int $trackingDevice = 0)
    {
        $dateObject = new DateTime($date);

        try {
            $interval = new DateInterval('P' . $lastDays . 'D');
            $dateObject->sub($interval);
            $today = $dateObject->format("Y-m-d") . " 00:00:00";
        } catch (\Exception $e) {
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
