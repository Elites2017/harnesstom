<?php

namespace App\Repository;

use App\Entity\Cross;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cross|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cross|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cross[]    findAll()
 * @method Cross[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CrossRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cross::class);
    }

    // /**
    //  * @return Cross[] Returns an array of Cross objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cross
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
