<?php

namespace App\Repository;

use App\Entity\DataSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataSubmission|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataSubmission|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataSubmission[]    findAll()
 * @method DataSubmission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataSubmission::class);
    }

    // /**
    //  * @return DataSubmission[] Returns an array of DataSubmission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DataSubmission
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
