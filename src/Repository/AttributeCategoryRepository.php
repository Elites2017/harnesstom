<?php

namespace App\Repository;

use App\Entity\AttributeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeCategory[]    findAll()
 * @method AttributeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeCategory::class);
    }

    // /**
    //  * @return AttributeCategory[] Returns an array of AttributeCategory objects
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
    public function findOneBySomeField($value): ?AttributeCategory
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
