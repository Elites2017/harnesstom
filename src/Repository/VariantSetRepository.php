<?php

namespace App\Repository;

use App\Entity\VariantSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VariantSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method VariantSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method VariantSet[]    findAll()
 * @method VariantSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VariantSet::class);
    }

    // /**
    //  * @return VariantSet[] Returns an array of VariantSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VariantSet
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
