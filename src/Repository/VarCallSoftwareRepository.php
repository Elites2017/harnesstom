<?php

namespace App\Repository;

use App\Entity\VarCallSoftware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VarCallSoftware|null find($id, $lockMode = null, $lockVersion = null)
 * @method VarCallSoftware|null findOneBy(array $criteria, array $orderBy = null)
 * @method VarCallSoftware[]    findAll()
 * @method VarCallSoftware[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VarCallSoftwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VarCallSoftware::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return VarCallSoftware[] Returns an array of VarCallSoftware objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VarCallSoftware
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
