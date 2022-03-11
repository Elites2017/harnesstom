<?php

namespace App\Repository;

use App\Entity\StudyParameterValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudyParameterValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudyParameterValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudyParameterValue[]    findAll()
 * @method StudyParameterValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudyParameterValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudyParameterValue::class);
    }

    // /**
    //  * @return StudyParameterValue[] Returns an array of StudyParameterValue objects
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
    public function findOneBySomeField($value): ?StudyParameterValue
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
