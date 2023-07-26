<?php

namespace App\Repository;

use App\Entity\PedigreeMirror;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PedigreeMirror|null find($id, $lockMode = null, $lockVersion = null)
 * @method PedigreeMirror|null findOneBy(array $criteria, array $orderBy = null)
 * @method PedigreeMirror[]    findAll()
 * @method PedigreeMirror[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PedigreeMirrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PedigreeMirror::class);
    }

    // /**
    //  * @return PedigreeMirror[] Returns an array of PedigreeMirror objects
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
    public function findOneBySomeField($value): ?PedigreeMirror
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
