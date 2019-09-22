<?php

namespace App\Repository;

use App\Entity\RpgMilestones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RpgMilestones|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpgMilestones|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpgMilestones[]    findAll()
 * @method RpgMilestones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpgMilestonesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgMilestones::class);
    }

    public function getLessThan($category, $value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :category')
            ->setParameter('category', $category)
            ->andWhere('r.value > :value')
            ->setParameter('value', $value)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getMoreThan($category, $value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :category')
            ->setParameter('category', $category)
            ->andWhere('r.value <= :value')
            ->setParameter('value', $value)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
