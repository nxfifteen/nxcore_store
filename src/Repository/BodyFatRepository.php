<?php

namespace App\Repository;

use App\Entity\BodyFat;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BodyFat|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyFat|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyFat[]    findAll()
 * @method BodyFat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyFatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyFat::class);
    }

    /**
     * @param String $patientId
     * @param String $date
     * @param int    $lastDays
     *
     * @return mixed
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
}
