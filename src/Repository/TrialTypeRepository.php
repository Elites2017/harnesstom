<?php

namespace App\Repository;

use App\Entity\TrialType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrialType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrialType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrialType[]    findAll()
 * @method TrialType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrialTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrialType::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return TrialType[] Returns an array of TrialType objects
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
    public function findOneBySomeField($value): ?TrialType
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
