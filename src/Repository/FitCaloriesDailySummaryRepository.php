<?php

namespace App\Repository;

use App\Entity\FitCaloriesDailySummary;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitCaloriesDailySummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitCaloriesDailySummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitCaloriesDailySummary[]    findAll()
 * @method FitCaloriesDailySummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitCaloriesDailySummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitCaloriesDailySummary::class);
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $lastDays
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function findByDateRangeHistorical(String $patientId, String $date, int $lastDays, int $trackingDevice)
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
}
