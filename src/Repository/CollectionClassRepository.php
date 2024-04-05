<?php

namespace App\Repository;

use App\Entity\CollectionClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectionClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionClass[]    findAll()
 * @method CollectionClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionClass::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('coll')
            ->select('count(coll.id)')
            ->where('coll.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('coll')
            ->select('coll.id, coll.name, coll.description')
            ->where('coll.isActive = 1');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('coll');
        $countQuery->select('COUNT(coll.id)')
            ->where('coll.isActive = 1');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "coll.name like :filter",
                    "coll.description like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "coll.name like :filter",
                    "coll.description like :filter"
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
                if ($order['name'] == 'name') {
                    $orderColumn = 'coll.name';
                }

                if ($order['name'] == 'description') {
                    $orderColumn = 'coll.description';
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
    //  * @return CollectionClass[] Returns an array of CollectionClass objects
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
    public function findOneBySomeField($value): ?CollectionClass
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
