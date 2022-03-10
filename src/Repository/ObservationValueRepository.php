<?php

namespace App\Repository;

use App\Entity\ObservationValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationValue[]    findAll()
 * @method ObservationValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObservationValue::class);
    }

    // /**
    //  * @return ObservationValue[] Returns an array of ObservationValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ObservationValue
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
