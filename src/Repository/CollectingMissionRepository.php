<?php

namespace App\Repository;

use App\Entity\CollectingMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectingMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectingMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectingMission[]    findAll()
 * @method CollectingMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectingMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectingMission::class);
    }

    // to show the number of accession by each collecting mission
    public function getAccessionsByCollectingMission() {
        $query = $this->createQueryBuilder('cm')
            ->select('cm as collectingMission, count(cm.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cm.isActive = 1')
            ->andWhere('cm.id = accession.collmissid')
            ->groupBy('cm.id')
            ->orderBy('count(cm.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return CollectingMission[] Returns an array of CollectingMission objects
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
    public function findOneBySomeField($value): ?CollectingMission
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
