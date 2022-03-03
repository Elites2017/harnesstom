<?php

namespace App\Repository;

use App\Entity\KinshipAlgorithm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method KinshipAlgorithm|null find($id, $lockMode = null, $lockVersion = null)
 * @method KinshipAlgorithm|null findOneBy(array $criteria, array $orderBy = null)
 * @method KinshipAlgorithm[]    findAll()
 * @method KinshipAlgorithm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KinshipAlgorithmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KinshipAlgorithm::class);
    }

    // /**
    //  * @return KinshipAlgorithm[] Returns an array of KinshipAlgorithm objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?KinshipAlgorithm
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
