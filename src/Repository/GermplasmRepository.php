<?php

namespace App\Repository;

use App\Entity\Germplasm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Germplasm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Germplasm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Germplasm[]    findAll()
 * @method Germplasm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GermplasmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Germplasm::class);
    }

    // /**
    //  * @return Germplasm[] Returns an array of Germplasm objects
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
    public function findOneBySomeField($value): ?Germplasm
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
