<?php

namespace App\Repository;

use App\Entity\Germplasm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Germplasm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Germplasm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Germplasm[]    findAll()
 * @method Germplasm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GermplasmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Germplasm::class);
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
        $query = $this->createQueryBuilder('germ')
            ->select("
                germ.id, germ.germplasmID, inst.id as inst_id, CONCAT(inst.instcode, ' - ', inst.name) as inst_instcode,
                germ.maintainerNumb, prog.id as prog_id, prog.abbreviation as prog_abbreviation"
                )
            ->join('App\Entity\Institute', 'inst')
            ->join('App\Entity\Program', 'prog')
            ->where('germ.isActive = 1')
            ->andWhere('germ.maintainerInstituteCode = inst.id')
            ->andWhere('germ.program = prog.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('germ');
        $countQuery->select('COUNT(germ.id)')
            ->join('App\Entity\Institute', 'inst')
            ->join('App\Entity\Program', 'prog')
            ->where('germ.isActive = 1')
            ->andWhere('germ.maintainerInstituteCode = inst.id')
            ->andWhere('germ.program = prog.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "germ.germplasmID like :filter",
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "germ.maintainerNumb like :filter",
                    "prog.abbreviation like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "germ.germplasmID like :filter",
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "germ.maintainerNumb like :filter",
                    "prog.abbreviation like :filter"
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
                if ($order['name'] == 'germplasmID') {
                    $orderColumn = 'germ.germplasmID';
                }

                if ($order['name'] == 'inst_instcode') {
                    $orderColumn = 'inst.instcode';
                }

                if ($order['name'] == 'maintainerNumb') {
                    $orderColumn = 'germ.maintainerNumb';
                }

                if ($order['name'] == 'prog_abbreviation') {
                    $orderColumn = 'prog.abbreviation';
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
    //  * @return Germplasm[] Returns an array of Germplasm objects
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
    public function findOneBySomeField($value): ?Germplasm
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
