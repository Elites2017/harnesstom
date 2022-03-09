<?php

namespace App\Repository;

use App\Entity\CollectionClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectionClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionClass[]    findAll()
 * @method CollectionClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionClass::class);
    }

    // /**
    //  * @return CollectionClass[] Returns an array of CollectionClass objects
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
    public function findOneBySomeField($value): ?CollectionClass
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
