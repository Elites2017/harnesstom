<?php

namespace App\Repository;

use App\Entity\ObservationValueOriginal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationValueOriginal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationValueOriginal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationValueOriginal[]    findAll()
 * @method ObservationValueOriginal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationValueOriginalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObservationValueOriginal::class);
    }

    // /**
    //  * @return ObservationValueOriginal[] Returns an array of ObservationValueOriginal objects
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
    public function findOneBySomeField($value): ?ObservationValueOriginal
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
