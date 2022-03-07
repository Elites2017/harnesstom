<?php

namespace App\Repository;

use App\Entity\VariantSetMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VariantSetMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method VariantSetMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method VariantSetMetadata[]    findAll()
 * @method VariantSetMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantSetMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VariantSetMetadata::class);
    }

    // /**
    //  * @return VariantSetMetadata[] Returns an array of VariantSetMetadata objects
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
    public function findOneBySomeField($value): ?VariantSetMetadata
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
