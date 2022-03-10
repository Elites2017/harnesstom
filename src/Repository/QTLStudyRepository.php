<?php

namespace App\Repository;

use App\Entity\QTLStudy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLStudy|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLStudy|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLStudy[]    findAll()
 * @method QTLStudy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLStudyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLStudy::class);
    }

    // /**
    //  * @return QTLStudy[] Returns an array of QTLStudy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QTLStudy
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
