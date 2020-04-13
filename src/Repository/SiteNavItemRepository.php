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

use App\Entity\SiteNavItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SiteNavItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteNavItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteNavItem[]    findAll()
 * @method SiteNavItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteNavItemRepository extends ServiceEntityRepository
{
    /**
     * SiteNavItemRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteNavItem::class);
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

    // /**
    //  * @return SiteNavItem[] Returns an array of SiteNavItem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiteNavItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
