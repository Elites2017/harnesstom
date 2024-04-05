<?php

namespace App\Repository;

use App\Entity\GWASVariant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GWASVariant|null find($id, $lockMode = null, $lockVersion = null)
 * @method GWASVariant|null findOneBy(array $criteria, array $orderBy = null)
 * @method GWASVariant[]    findAll()
 * @method GWASVariant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GWASVariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GWASVariant::class);
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
        $query = $this->createQueryBuilder('gv')
            ->select("
                gv.id, gv.name as gv_name, mk.linkageGroupName as mk_linkage_group_name,
                mk.position as mk_position, mk.id as mk_id, mk.name as mk_name, gv.maf as gv_maf,
                gv.sampleSize as gv_sample_size, gv.snppValue as gv_snpp_value,
                gv.adjustedPValue as gv_adjusted_pvalue, gv.allelicEffect as gv_allelic_effect,
                gs.id as gs_id, gs.name as gs_name"
                )
            ->join('App\Entity\Marker', 'mk')
            ->join('App\Entity\GWAS', 'gs')
            ->where('gv.isActive = 1')
            ->andWhere('gv.marker = mk.id')
            ->andWhere('gv.gwas = gs.id');

        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('gv');
        $countQuery->select('COUNT(gv.id)')
            ->join('App\Entity\Marker', 'mk')
            ->join('App\Entity\GWAS', 'gs')
            ->where('gv.isActive = 1')
            ->andWhere('gv.marker = mk.id')
            ->andWhere('gv.gwas = gs.id');

        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "gv.name like :filter",
                    "mk.linkageGroupName like :filter",
                    "mk.position like :filter",
                    "mk.name like :filter",
                    "gv.maf like :filter",
                    "gv.sampleSize like :filter",
                    "gv.adjustedPValue like :filter",
                    "gv.allelicEffect like :filter",
                    "gs.name like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "gv.name like :filter",
                    "mk.linkageGroupName like :filter",
                    "mk.position like :filter",
                    "mk.name like :filter",
                    "gv.maf like :filter",
                    "gv.sampleSize like :filter",
                    "gv.adjustedPValue like :filter",
                    "gv.allelicEffect like :filter",
                    "gs.name like :filter"
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
                if ($order['name'] == 'gv_name') {
                    $orderColumn = 'gv.name';
                }

                if ($order['name'] == 'mk_linkage_group_name') {
                    $orderColumn = 'mk.linkageGroupName';
                }

                if ($order['name'] == 'mk_position') {
                    $orderColumn = 'mk.position';
                }

                if ($order['name'] == 'mk_name') {
                    $orderColumn = 'mk.name';
                }

                if ($order['name'] == 'gv_maf') {
                    $orderColumn = 'gv.maf';
                }

                if ($order['name'] == 'gv_sample_size') {
                    $orderColumn = 'gv.sampleSize';
                }

                if ($order['name'] == 'gv_adjusted_pvalue') {
                    $orderColumn = 'gv.adjustedPValue';
                }

                if ($order['name'] == 'gv_allelic_effect') {
                    $orderColumn = 'gv.allelicEffect';
                }

                if ($order['name'] == 'gs_name') {
                    $orderColumn = 'gs.name';
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
    //  * @return GWASVariant[] Returns an array of GWASVariant objects
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
    public function findOneBySomeField($value): ?GWASVariant
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
