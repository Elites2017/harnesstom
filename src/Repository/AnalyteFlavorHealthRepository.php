<?php

namespace App\Repository;

use App\Entity\AnalyteFlavorHealth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalyteFlavorHealth|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyteFlavorHealth|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyteFlavorHealth[]    findAll()
 * @method AnalyteFlavorHealth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyteFlavorHealthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyteFlavorHealth::class);
    }

    // /**
    //  * @return AnalyteFlavorHealth[] Returns an array of AnalyteFlavorHealth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnalyteFlavorHealth
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
