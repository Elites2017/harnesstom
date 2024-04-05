<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('prg')
            ->select('count(prg.id)')
            ->where('prg.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('prg')
            ->select('prg.id, prg.abbreviation, prg.name, crop.id as crop_id, crop.commonCropName as crop_name')
            ->join('App\Entity\Crop', 'crop')
            ->where('prg.isActive = 1')
            ->andWhere('prg.crop = crop.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('prg');
        $countQuery->select('COUNT(prg.id)')
            ->join('App\Entity\Crop', 'crop')
            ->where('prg.isActive = 1')
            ->andWhere('prg.crop = crop.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "prg.abbreviation like :filter",
                    "prg.name like :filter",
                    "crop.commonCropName like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "prg.abbreviation like :filter",
                    "prg.name like :filter",
                    "crop.commonCropName like :filter"
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
                if ($order['name'] == 'abbreviation') {
                    $orderColumn = 'prg.abbreviation';
                }

                if ($order['name'] == 'name') {
                    $orderColumn = 'prg.name';
                }

                if ($order['name'] == 'crop_name') {
                    $orderColumn = 'crop.name';
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
    //  * @return Program[] Returns an array of Program objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Program
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
