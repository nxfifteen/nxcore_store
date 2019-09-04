<?php

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiAccessLog::class);
    }

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


    /*
    public function findOneBySomeField($value): ?ApiAccessLog
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
