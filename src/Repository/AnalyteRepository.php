<?php

namespace App\Repository;

use App\Entity\Analyte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Analyte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analyte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analyte[]    findAll()
 * @method Analyte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analyte::class);
    }

    // /**
    //  * @return Analyte[] Returns an array of Analyte objects
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
    public function findOneBySomeField($value): ?Analyte
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
