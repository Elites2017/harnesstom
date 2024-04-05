<?php

namespace App\Repository;

use App\Entity\QTLVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QTLVariant|null find($id, $lockMode = null, $lockVersion = null)
 * @method QTLVariant|null findOneBy(array $criteria, array $orderBy = null)
 * @method QTLVariant[]    findAll()
 * @method QTLVariant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QTLVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QTLVariant::class);
    }

    public function totalRows() {
        return $this->createQueryBuilder('tab')
            ->select('count(tab.id)')
            ->where('tab.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('qv')
            ->select("
                qv.id, qv.name as qv_name, qv.linkageGroupName as qv_linkage_group_name,
                qv.peakPosition as qv_peak_position, mk.id as mk_closest_id, mk.name as mk_closest_name,
                qs.id as qs_id, qs.name as qs_name, qstat.id as qstat_id, qstat.name as qstat_name,
                qv.qtlStatsValue as qv_qtl_stats_value, qv.r2 as qv_r2"
                )
            ->join('App\Entity\Marker', 'mk')
            ->join('App\Entity\QTLStudy', 'qs')
            ->join('App\Entity\QTLStatistic', 'qstat')
            ->where('qv.isActive = 1')
            ->andWhere('qv.closestMarker = mk.id')
            ->andWhere('qv.qtlStudy = qs.id')
            ->andWhere('qs.statistic = qstat.id');

        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('qv');
        $countQuery->select('COUNT(qv.id)')
            ->join('App\Entity\Marker', 'mk')
            ->join('App\Entity\QTLStudy', 'qs')
            ->join('App\Entity\QTLStatistic', 'qstat')
            ->where('qv.isActive = 1')
            ->andWhere('qv.closestMarker = mk.id')
            ->andWhere('qv.qtlStudy = qs.id')
            ->andWhere('qs.statistic = qstat.id');

        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "qv.name like :filter",
                    "qv.linkageGroupName like :filter",
                    "qv.peakPosition like :filter",
                    "mk.name like :filter",
                    "qs.name like :filter",
                    "qstat.name like :filter",
                    "qv.qtlStatsValue like :filter",
                    "qv.r2 like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "qv.name like :filter",
                    "qv.linkageGroupName like :filter",
                    "qv.peakPosition like :filter",
                    "mk.name like :filter",
                    "qs.name like :filter",
                    "qstat.name like :filter",
                    "qv.qtlStatsValue like :filter",
                    "qv.r2 like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;
        }
                
        // Limit
        $query->setFirstResult($start)->setMaxResults($length);

        // Order
        foreach ($orders as $key => $order)
        {
            // $order['name'] is the name of the order column as sent by the JS
            if ($order['name'] != '')
            {
                $orderColumn = null;
                if ($order['name'] == 'qv_name') {
                    $orderColumn = 'qv.name';
                }

                if ($order['name'] == 'qv_linkage_group_name') {
                    $orderColumn = 'qv.linkageGroupName';
                }

                if ($order['name'] == 'qv_peakPosition') {
                    $orderColumn = 'qv.peakPosition';
                }

                if ($order['name'] == 'mk_closest_name') {
                    $orderColumn = 'mk.name';
                }

                if ($order['name'] == 'qs_name') {
                    $orderColumn = 'qs.name';
                }

                if ($order['name'] == 'qstat_name') {
                    $orderColumn = 'qstat.name';
                }

                if ($order['name'] == 'qv_qtl_stats_value') {
                    $orderColumn = 'qv.qtlStatsValue';
                }

                if ($order['name'] == 'qv_r2') {
                    $orderColumn = 'qv.r2';
                }

                if ($orderColumn !== null)
                {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }
        
        // Execute
        $results = $query->getQuery()->getArrayResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        
        // data returned
        $rawDatatable = [];
        $rawDatatable = [
            "results" => $results,
            "countResult" => $countResult
        ];
        return $rawDatatable;
    }

    // /**
    //  * @return QTLVariant[] Returns an array of QTLVariant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QTLVariant
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
