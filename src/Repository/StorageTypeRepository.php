<?php

namespace App\Repository;

use App\Entity\StorageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageType|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageType|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageType[]    findAll()
 * @method StorageType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StorageTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageType::class);
    }

    // /**
    //  * @return StorageType[] Returns an array of StorageType objects
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
    public function findOneBySomeField($value): ?StorageType
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
