<?php

namespace App\Repository;

use App\Entity\AttributeTraitValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeTraitValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeTraitValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeTraitValue[]    findAll()
 * @method AttributeTraitValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeTraitValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeTraitValue::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('atv')
            ->select('count(atv.id)')
            ->where('atv.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('atv')
            ->select("
                atv.id, acc.id as acc_id, acc.accename as acc_name, att.id as att_id, att.name as att_name, atc.id as atc_id, atc.name as atc_name,
                tr.id as tr_id, tr.name as tr_name, atv.value as value, atv.publicationReference as pub_ref"
                )
            ->join('App\Entity\Accession', 'acc')
            ->join('App\Entity\Attribute', 'att')
            ->join('App\Entity\AttributeCategory', 'atc')
            ->join('App\Entity\TraitClass', 'tr')
            ->where('atv.isActive = 1')
            ->andWhere('atv.accession = acc.id')
            ->andWhere('atv.attribute = att.id')
            ->andWhere('atv.trait = tr.id')
            ->andWhere('att.category = atc.id');
            
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('atv');
        $countQuery->select('COUNT(atv.id)')
            ->join('App\Entity\Accession', 'acc')
            ->join('App\Entity\Attribute', 'att')
            ->join('App\Entity\AttributeCategory', 'atc')
            ->join('App\Entity\TraitClass', 'tr')
            ->where('atv.isActive = 1')
            ->andWhere('atv.accession = acc.id')
            ->andWhere('atv.attribute = att.id')
            ->andWhere('atv.trait = tr.id')
            ->andWhere('att.category = atc.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "acc.name like :filter",
                    "att.name like :filter",
                    "atc.name like :filter",
                    "tr.name like :filter",
                    "atv.value like :filter",
                    "atv.publicationReference like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "acc.name like :filter",
                    "att.name like :filter",
                    "atc.name like :filter",
                    "tr.name like :filter",
                    "atv.value like :filter",
                    "atv.publicationReference like :filter"
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
                if ($order['name'] == 'acc_name') {
                    $orderColumn = 'acc.name';
                }

                if ($order['name'] == 'att_name') {
                    $orderColumn = 'att.name';
                }

                if ($order['name'] == 'atc_name') {
                    $orderColumn = 'atc.name';
                }

                if ($order['name'] == 'tr_name') {
                    $orderColumn = 'tr.name';
                }

                if ($order['name'] == 'value') {
                    $orderColumn = 'atv.value';
                }

                if ($order['name'] == 'pub_ref') {
                    $orderColumn = 'atv.publicationReference';
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
    //  * @return AttributeTraitValue[] Returns an array of AttributeTraitValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributeTraitValue
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
