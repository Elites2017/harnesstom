<?php

namespace App\Repository;

use App\Entity\ParameterValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParameterValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParameterValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParameterValue[]    findAll()
 * @method ParameterValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParameterValue::class);
    }

    // /**
    //  * @return ParameterValue[] Returns an array of ParameterValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ParameterValue
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
