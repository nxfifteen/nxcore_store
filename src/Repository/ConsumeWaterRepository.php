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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsumeWater::class);
    }

    public function findByDateRange( String $patientId, String $date ) {
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
