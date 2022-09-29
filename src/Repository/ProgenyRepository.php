<?php

namespace App\Repository;

use App\Entity\Progeny;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Progeny|null find($id, $lockMode = null, $lockVersion = null)
 * @method Progeny|null findOneBy(array $criteria, array $orderBy = null)
 * @method Progeny[]    findAll()
 * @method Progeny[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgenyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Progeny::class);
    }

    // /**
    //  * @return Progeny[] Returns an array of Progeny objects
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
    public function findOneBySomeField($value): ?Progeny
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
