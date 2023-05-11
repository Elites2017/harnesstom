<?php

namespace App\Repository;

use App\Entity\Institute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institute[]    findAll()
 * @method Institute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstituteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institute::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('inst')
            ->select('count(inst.id)')
            ->where('inst.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // to show the number of accession by maintaining institute
    public function getAccessionsByMaintainingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.instcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by donor institute
    public function getAccessionsByDonorInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.donorcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by breding institute
    public function getAccessionsByBreedingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst as institute, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.bredcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id, inst.instcode, inst.acronym, inst.name, ctry.id as country_id, ctry.iso3 as country_iso3')
            ->join('App\Entity\Country', 'ctry')
            ->where('inst.isActive = 1')
            ->andWhere('inst.country = ctry.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('inst');
        $countQuery->select('COUNT(inst.id)')
            ->join('App\Entity\Country', 'ctry')
            ->where('inst.isActive = 1')
            ->andWhere('inst.country = ctry.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "inst.acronym like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "inst.acronym like :filter"
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
                if ($order['name'] == 'instcode') {
                    $orderColumn = 'inst.instcode';
                }

                if ($order['name'] == 'acronym') {
                    $orderColumn = 'inst.acronym';
                }

                if ($order['name'] == 'name') {
                    $orderColumn = 'inst.name';
                }

                if ($order['name'] == 'country_iso3') {
                    $orderColumn = 'ctry.iso3';
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
    //  * @return Institute[] Returns an array of Institute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Institute
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
