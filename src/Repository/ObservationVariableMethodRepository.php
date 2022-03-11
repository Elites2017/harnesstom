<?php

namespace App\Repository;

use App\Entity\ObservationVariableMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationVariableMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationVariableMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationVariableMethod[]    findAll()
 * @method ObservationVariableMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationVariableMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObservationVariableMethod::class);
    }

    // /**
    //  * @return ObservationVariableMethod[] Returns an array of ObservationVariableMethod objects
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
    public function findOneBySomeField($value): ?ObservationVariableMethod
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
