<?php

namespace App\Repository;

use App\Entity\Pedigree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pedigree|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pedigree|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pedigree[]    findAll()
 * @method Pedigree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedigreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedigree::class);
    }

    // /**
    //  * @return Pedigree[] Returns an array of Pedigree objects
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
    public function findOneBySomeField($value): ?Pedigree
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
