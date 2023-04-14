<?php

namespace App\Repository;

use App\Entity\CollectingSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectingSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectingSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectingSource[]    findAll()
 * @method CollectingSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectingSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectingSource::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each collecting source
    public function getAccessionsByCollectingSource() {
        $query = $this->createQueryBuilder('cs')
            ->select('cs as collectingSource, count(cs.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cs.isActive = 1')
            ->andWhere('cs.id = accession.collsrc')
            ->groupBy('cs.id')
            ->orderBy('count(cs.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return CollectingSource[] Returns an array of CollectingSource objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CollectingSource
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
