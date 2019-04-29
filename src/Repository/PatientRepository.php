<?php

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
