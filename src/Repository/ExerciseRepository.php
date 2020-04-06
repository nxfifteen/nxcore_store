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

use App\Entity\Exercise;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Exercise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercise[]    findAll()
 * @method Exercise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseRepository extends ServiceEntityRepository
{
    /**
     * ExerciseRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
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

        return $this->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p')
            ->andWhere('c.dateTimeStart >= :val')
            ->setParameter('val', $today)
            ->andWhere('c.dateTimeStart <= :valEnd')
            ->setParameter('valEnd', $todayEnd)
            ->andWhere('p.uuid = :patientId')
            ->setParameter('patientId', $patientId)
            ->orderBy('c.dateTimeStart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
