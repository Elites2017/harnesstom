<?php

namespace App\Repository;

use App\Entity\BiologicalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiologicalStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiologicalStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiologicalStatus[]    findAll()
 * @method BiologicalStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiologicalStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BiologicalStatus::class);
    }

    // /**
    //  * @return BiologicalStatus[] Returns an array of BiologicalStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BiologicalStatus
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}