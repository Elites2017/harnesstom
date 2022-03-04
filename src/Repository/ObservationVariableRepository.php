<?php

namespace App\Repository;

use App\Entity\ObservationVariable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationVariable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationVariable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationVariable[]    findAll()
 * @method ObservationVariable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationVariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObservationVariable::class);
    }

    // /**
    //  * @return ObservationVariable[] Returns an array of ObservationVariable objects
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
    public function findOneBySomeField($value): ?ObservationVariable
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
