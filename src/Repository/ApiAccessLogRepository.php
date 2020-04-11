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

use App\Entity\ApiAccessLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method ApiAccessLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiAccessLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiAccessLog[]    findAll()
 * @method ApiAccessLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiAccessLogRepository extends ServiceEntityRepository
{
    /**
     * ApiAccessLogRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiAccessLog::class);
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
     * @param $patient
     * @param $service
     * @param $entity
     *
     * @return ApiAccessLog|null
     */
    public function findLastAccess($patient, $service, $entity): ?ApiAccessLog
    {
        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.patient = :patientId')
                ->setParameter('patientId', $patient)
                ->andWhere('a.thirdPartyService = :serviceId')
                ->setParameter('serviceId', $service)
                ->andWhere('a.entity = :val')
                ->setParameter('val', $entity)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
