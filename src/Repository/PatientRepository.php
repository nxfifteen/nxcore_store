<?php

/*
 * This file is part of the Storage module in NxFIFTEEN Core.
 *
 * Copyright (c) 2019. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Store
 * @version     0.0.0.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://git.nxfifteen.rocks/nx-health NxFIFTEEN Core
 * @link        https://git.nxfifteen.rocks/nx-health/store NxFIFTEEN Core Storage
 * @copyright   2019 Stuart McCulloch Anderson
 * @license     https://license.nxfifteen.rocks/mit/2015-2019/ MIT
 */
    
    namespace App\Repository;

    use App\Entity\Patient;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\ORM\NonUniqueResultException;
    use Symfony\Bridge\Doctrine\RegistryInterface;

    /**
     * @method Patient|null find( $id, $lockMode = NULL, $lockVersion = NULL )
     * @method Patient|null findOneBy( array $criteria, array $orderBy = NULL )
     * @method Patient[]    findAll()
     * @method Patient[]    findBy( array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL )
     */
    class PatientRepository extends ServiceEntityRepository
    {
        public function __construct( RegistryInterface $registry )
        {
            parent::__construct($registry, Patient::class);
        }

        public function findByUuid( $value ): ?Patient
        {
            $matched = $this->createQueryBuilder('p')
                ->andWhere('p.uuid = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getResult();

            return array_pop($matched);
        }
    }
