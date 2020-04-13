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
    /**
     * RpgMilestonesRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpgMilestones::class);
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
     * @param $category
     * @param $value
     *
     * @return mixed
     */
    public function getLessThan($category, $value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :category')
            ->setParameter('category', $category)
            ->andWhere('r.value > :value')
            ->setParameter('value', $value)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $category
     * @param $value
     *
     * @return mixed
     */
    public function getMoreThan($category, $value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :category')
            ->setParameter('category', $category)
            ->andWhere('r.value <= :value')
            ->setParameter('value', $value)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
