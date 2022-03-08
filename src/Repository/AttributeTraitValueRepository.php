<?php

namespace App\Repository;

use App\Entity\AttributeTraitValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeTraitValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeTraitValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeTraitValue[]    findAll()
 * @method AttributeTraitValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeTraitValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeTraitValue::class);
    }

    // /**
    //  * @return AttributeTraitValue[] Returns an array of AttributeTraitValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributeTraitValue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
