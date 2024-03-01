<?php

namespace App\Repository;

use App\Entity\QTLEpistasisEffect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLEpistasisEffect|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLEpistasisEffect|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLEpistasisEffect[]    findAll()
 * @method QTLEpistasisEffect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLEpistasisEffectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLEpistasisEffect::class);
    }

    // /**
    //  * @return QTLEpistasisEffect[] Returns an array of QTLEpistasisEffect objects
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
    public function findOneBySomeField($value): ?QTLEpistasisEffect
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
