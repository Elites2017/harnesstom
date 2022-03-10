<?php

namespace App\Repository;

use App\Entity\QTLEpistatisticEffect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLEpistatisticEffect|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLEpistatisticEffect|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLEpistatisticEffect[]    findAll()
 * @method QTLEpistatisticEffect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLEpistatisticEffectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLEpistatisticEffect::class);
    }

    // /**
    //  * @return QTLEpistatisticEffect[] Returns an array of QTLEpistatisticEffect objects
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
    public function findOneBySomeField($value): ?QTLEpistatisticEffect
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
