<?php

namespace App\Repository;

use App\Entity\BreedingMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BreedingMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method BreedingMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method BreedingMethod[]    findAll()
 * @method BreedingMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BreedingMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BreedingMethod::class);
    }

    // /**
    //  * @return BreedingMethod[] Returns an array of BreedingMethod objects
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
    public function findOneBySomeField($value): ?BreedingMethod
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
