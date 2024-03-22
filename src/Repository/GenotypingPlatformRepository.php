<?php

namespace App\Repository;

use App\Entity\GenotypingPlatform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GenotypingPlatform|null find($id, $lockMode = null, $lockVersion = null)
 * @method GenotypingPlatform|null findOneBy(array $criteria, array $orderBy = null)
 * @method GenotypingPlatform[]    findAll()
 * @method GenotypingPlatform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenotypingPlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenotypingPlatform::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('gpf')
            ->select('count(gpf.id)')
            ->where('gpf.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('gpf')
            ->select('sqt.id as sqt_id, sqt.name as sqt_name, gpf.id, gpf.name, gpf.refSetName as ref_set_name,
                    gpf.markerCount as marker_count, sqinst.id as sqinst_id, sqinst.name as sqinst_name')
            ->join('App\Entity\SequencingType', 'sqt')
            ->join('App\Entity\SequencingInstrument', 'sqinst')
            ->where('gpf.isActive = 1')
            ->andWhere('gpf.sequencingType = sqt.id')
            ->andWhere('gpf.sequencingInstrument = sqinst.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('gpf');
        $countQuery->select('COUNT(gpf.id)')
            ->join('App\Entity\SequencingType', 'sqt')
            ->join('App\Entity\SequencingInstrument', 'sqinst')
            ->where('gpf.isActive = 1')
            ->andWhere('gpf.sequencingType = sqt.id')
            ->andWhere('gpf.sequencingInstrument = sqinst.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "gpf.name like :filter",
                    "gpf.refSetName like :filter",
                    "gpf.markerCount like :filter",
                    "sqt.name like :filter",
                    "sqinst.name like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "gpf.name like :filter",
                    "gpf.refSetName like :filter",
                    "gpf.markerCount like :filter",
                    "sqt.name like :filter",
                    "sqinst.name like :filter"
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
                if ($order['name'] == 'sqt_name') {
                    $orderColumn = 'sqt.name';
                }

                if ($order['name'] == 'name') {
                    $orderColumn = 'gpf.name';
                }

                if ($order['name'] == 'ref_set_name') {
                    $orderColumn = 'gpf.refSetName';
                }

                if ($order['name'] == 'marker_count') {
                    $orderColumn = 'gpf.markerCount';
                }

                if ($order['name'] == 'sqinst_name') {
                    $orderColumn = 'sqinst.name';
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
    //  * @return GenotypingPlatform[] Returns an array of GenotypingPlatform objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GenotypingPlatform
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
