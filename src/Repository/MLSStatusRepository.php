<?php

namespace App\Repository;

use App\Entity\MLSStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MLSStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MLSStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MLSStatus[]    findAll()
 * @method MLSStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MLSStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MLSStatus::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each mls status
    public function getAccessionsByMLSStatus() {
        $query = $this->createQueryBuilder('mlsS')
            ->select('mlsS as mlsStatus, count(mlsS.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('mlsS.isActive = 1')
            ->andWhere('mlsS.id = accession.mlsStatus')
            ->groupBy('mlsS.id')
            ->orderBy('count(mlsS.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return MLSStatus[] Returns an array of MLSStatus objects
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
    public function findOneBySomeField($value): ?MLSStatus
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
