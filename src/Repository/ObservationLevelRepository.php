<?php

namespace App\Repository;

use App\Entity\ObservationLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationLevel[]    findAll()
 * @method ObservationLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObservationLevel::class);
    }

    // /**
    //  * @return ObservationLevel[] Returns an array of ObservationLevel objects
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
    public function findOneBySomeField($value): ?ObservationLevel
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
