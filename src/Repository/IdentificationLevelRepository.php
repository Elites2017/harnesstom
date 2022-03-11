<?php

namespace App\Repository;

use App\Entity\IdentificationLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IdentificationLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdentificationLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdentificationLevel[]    findAll()
 * @method IdentificationLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentificationLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdentificationLevel::class);
    }

    // /**
    //  * @return IdentificationLevel[] Returns an array of IdentificationLevel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IdentificationLevel
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
