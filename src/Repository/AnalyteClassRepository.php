<?php

namespace App\Repository;

use App\Entity\AnalyteClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalyteClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyteClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyteClass[]    findAll()
 * @method AnalyteClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyteClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyteClass::class);
    }

    // /**
    //  * @return AnalyteClass[] Returns an array of AnalyteClass objects
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
    public function findOneBySomeField($value): ?AnalyteClass
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
