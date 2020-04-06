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

use App\AppConstants;
use App\Entity\BodyWeight;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method BodyWeight|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method BodyWeight|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method BodyWeight[]    findAll()
 * @method BodyWeight[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class BodyWeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyWeight::class);
    }

    /**
     * @param String $patientId
     * @param String $date
     *
     * @return mixed
     */
    public function findByDateRange(String $patientId, String $date)
    {
        return $this->findByDateRangeHistorical($patientId, $date, 0);
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $lastDays
     *
     * @return mixed
     * @throws \Exception
     */
    public function findByDateRangeHistorical(String $patientId, String $date, int $lastDays)
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

        /** @var BodyWeight[] $weightRecords */
        $weightRecords = $this->createQueryBuilder('c')
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

        if (count($weightRecords) == 0) {
            return [];
        }

        /** @var DateTime[] $period */
        $period = new DatePeriod(
            $dateObject,
            new DateInterval('P1D'),
            new DateTime($date)
        );
        $dateArray = [];
        foreach ($period as $key => $value) {
            $dateArray[] = $value;
        }

        $loopDateCount = 0;
        $loopWeightCount = 0;
        $weightReturnData = [];
        /** @var BodyWeight $previousWeightRecord */
        $previousWeightRecord = NULL;
        for ($i = 0; $i <= ($lastDays - 1); $i++){
            if ($weightRecords[$loopWeightCount]->getDateTime()->format("Y-m-d") == $dateArray[$i]->format('Y-m-d')) {
                $previousWeightRecord = clone $weightRecords[$loopWeightCount];
                $weightReturnData[] = clone $weightRecords[$loopWeightCount];
                $loopWeightCount++;
            } else {
                if (!is_null($previousWeightRecord)) {
                    $previousWeightRecord->setDateTime($dateArray[$i]);
                    $weightReturnData[] = clone $previousWeightRecord;
                }
            }
        }

        return $weightReturnData;
    }

    /**
     * @param String $patientId
     *
     * @return mixed
     */
    public function findFirst(String $patientId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.uuid = :patientId')
            ->setParameter('patientId', $patientId)
            ->orderBy('c.DateTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param String $patientId
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findLast(String $patientId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.uuid = :patientId')
            ->setParameter('patientId', $patientId)
            ->orderBy('c.DateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param String             $patientId
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return mixed
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function findSevenDayAgo(String $patientId, \DateTimeInterface $dateTime)
    {
        $interval = new DateInterval('P6D');
        $dateTime->sub($interval);

        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $patientId)
            ->andWhere('c.DateTime < :currentDateTime')
            ->setParameter('currentDateTime', $dateTime->format("Y-m-d 00:00:00"))
            ->orderBy('c.DateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param String             $patientId
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findSevenDayAverage(String $patientId, \DateTimeInterface $dateTime)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $patientId)
            ->andWhere('c.DateTime <= :currentDateTime')
            ->setParameter('currentDateTime', $dateTime->format("Y-m-d 00:00:00"))
            ->orderBy('c.DateTime', 'DESC')
            ->select('avg(c.measurement) as avg')
            ->getQuery()->getOneOrNullResult()['avg'];
    }

    /**
     * @param String             $patientId
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findPrevious(String $patientId, \DateTimeInterface $dateTime)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('p.id = :patientId')
            ->setParameter('patientId', $patientId)
            ->andWhere('c.DateTime < :currentDateTime')
            ->setParameter('currentDateTime', $dateTime->format("Y-m-d 00:00:00"))
            ->orderBy('c.DateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param String $patientId
     * @param int    $trackingDevice
     *
     * @return mixed
     */
    public function getSumOfValues(String $patientId, int $trackingDevice)
    {
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
    }
}
