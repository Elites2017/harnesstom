<?php

namespace App\Repository;

use App\Entity\GWASModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GWASModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method GWASModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method GWASModel[]    findAll()
 * @method GWASModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GWASModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GWASModel::class);
    }

    // /**
    //  * @return GWASModel[] Returns an array of GWASModel objects
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
    public function findOneBySomeField($value): ?GWASModel
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
