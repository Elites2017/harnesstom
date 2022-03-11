<?php

namespace App\Repository;

use App\Entity\Synonym;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Synonym|null find($id, $lockMode = null, $lockVersion = null)
 * @method Synonym|null findOneBy(array $criteria, array $orderBy = null)
 * @method Synonym[]    findAll()
 * @method Synonym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SynonymRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Synonym::class);
    }

    // /**
    //  * @return Synonym[] Returns an array of Synonym objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Synonym
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
