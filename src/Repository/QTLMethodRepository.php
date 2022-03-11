<?php

namespace App\Repository;

use App\Entity\QTLMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLMethod[]    findAll()
 * @method QTLMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLMethod::class);
    }

    // /**
    //  * @return QTLMethod[] Returns an array of QTLMethod objects
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
    public function findOneBySomeField($value): ?QTLMethod
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
