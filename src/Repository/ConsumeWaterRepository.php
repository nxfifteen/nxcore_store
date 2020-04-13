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

use App\Entity\ConsumeWater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ConsumeWater|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsumeWater|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsumeWater[]    findAll()
 * @method ConsumeWater[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsumeWaterRepository extends ServiceEntityRepository
{
    /**
     * ConsumeWaterRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsumeWater::class);
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
     * @param String $date
     *
     * @return mixed
     */
    public function findByDateRange(string $patientId, string $date)
    {
        $today = $date . " 00:00:00";
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

}
