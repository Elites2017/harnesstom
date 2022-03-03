<?php

namespace App\Repository;

use App\Entity\ScaleCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScaleCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScaleCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScaleCategory[]    findAll()
 * @method ScaleCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScaleCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScaleCategory::class);
    }

    // /**
    //  * @return ScaleCategory[] Returns an array of ScaleCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScaleCategory
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
