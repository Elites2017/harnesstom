<?php

namespace App\Repository;

use App\Entity\GWAS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GWAS|null find($id, $lockMode = null, $lockVersion = null)
 * @method GWAS|null findOneBy(array $criteria, array $orderBy = null)
 * @method GWAS[]    findAll()
 * @method GWAS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GWASRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GWAS::class);
    }

    // /**
    //  * @return GWAS[] Returns an array of GWAS objects
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
    public function findOneBySomeField($value): ?GWAS
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
