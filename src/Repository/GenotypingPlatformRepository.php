<?php

namespace App\Repository;

use App\Entity\GenotypingPlatform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GenotypingPlatform|null find($id, $lockMode = null, $lockVersion = null)
 * @method GenotypingPlatform|null findOneBy(array $criteria, array $orderBy = null)
 * @method GenotypingPlatform[]    findAll()
 * @method GenotypingPlatform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenotypingPlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenotypingPlatform::class);
    }

    // /**
    //  * @return GenotypingPlatform[] Returns an array of GenotypingPlatform objects
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
    public function findOneBySomeField($value): ?GenotypingPlatform
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
