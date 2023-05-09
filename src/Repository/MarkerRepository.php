<?php

namespace App\Repository;

use App\Entity\Marker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Marker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marker[]    findAll()
 * @method Marker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marker::class);
    }

    public function myMarker() {
        $query = $this->createQueryBuilder('mkr')
            ->where('mkr.isActive = 1')
            ;
        
        return $query->getQuery()->getArrayResult();

    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('mkr')
            ->select('count(mkr.id)')
            ->where('mkr.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('mkr');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('mkr');
        $countQuery->select('COUNT(mkr.id)');
        
        // Other conditions than the ones sent by the Ajax call ?
        if ($otherConditions === null)
        {
            // No
            // However, add a "always true" condition to keep an uniform treatment in all cases
            $query->where("1=1");
            $countQuery->where("1=1");
        }
        if($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "mkr.name like :filter",
                    "mkr.position like :filter",
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "mkr.name like :filter",
                    "mkr.position like :filter",
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;
        }
        if ($otherConditions != null)
        {
            // Add condition
            $query->where($otherConditions);
            $countQuery->where($otherConditions);
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
            
                switch($order['name'])
                {
                    case 'name':
                    {
                        $orderColumn = 'mkr.name';
                        break;
                    }
                    case 'position':
                    {
                        $orderColumn = 'mkr.position';
                        break;
                    }
                }
        
                if ($orderColumn !== null)
                {
                    $query->orderBy($orderColumn, $order['dir']);
                }
            }
        }
        
        // Execute
        $results = $query->getQuery()->getArrayResult();
        //dd($query->getDQL());
        $countResult = $countQuery->getQuery()->getSingleScalarResult();
        
        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );
    }

    // /**
    //  * @return Marker[] Returns an array of Marker objects
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
    public function findOneBySomeField($value): ?Marker
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
