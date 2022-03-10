<?php

namespace App\Repository;

use App\Entity\GWASVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GWASVariant|null find($id, $lockMode = null, $lockVersion = null)
 * @method GWASVariant|null findOneBy(array $criteria, array $orderBy = null)
 * @method GWASVariant[]    findAll()
 * @method GWASVariant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GWASVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GWASVariant::class);
    }

    // /**
    //  * @return GWASVariant[] Returns an array of GWASVariant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GWASVariant
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
