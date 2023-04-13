<?php

namespace App\Repository;

use App\Entity\FactorType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactorType[]    findAll()
 * @method FactorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactorTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactorType::class);
    }

    // /**
    //  * @return FactorType[] Returns an array of FactorType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FactorType
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
