<?php

namespace App\Repository;

use App\Entity\TraitClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TraitClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method TraitClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method TraitClass[]    findAll()
 * @method TraitClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TraitClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TraitClass::class);
    }

    // /**
    //  * @return TraitClass[] Returns an array of TraitClass objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TraitClass
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
