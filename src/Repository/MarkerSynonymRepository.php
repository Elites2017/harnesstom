<?php

namespace App\Repository;

use App\Entity\MarkerSynonym;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MarkerSynonym|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarkerSynonym|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarkerSynonym[]    findAll()
 * @method MarkerSynonym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkerSynonymRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarkerSynonym::class);
    }

    // /**
    //  * @return MarkerSynonym[] Returns an array of MarkerSynonym objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MarkerSynonym
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
