<?php

namespace App\Repository;

use App\Entity\Sample;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sample|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sample|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sample[]    findAll()
 * @method Sample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SampleRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, Sample::class);
    }

    public function findReleasedTrialStudySample($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('spl')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('spl.isActive = 1')
            ->Where('spl.study = st.id')
            ->andWhere('st.trial = tr.id')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;

        if ($user) {
            // check if any trial is shared with this user
            // 0 means no trial is shared with this user
            // in this case show the studies where this user
            // is the owner of future trials used in those studies
            if ($this->swRepo->totalRows($user) == 0) {
                $query->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
            
            // if one trial is shared with this user
            // include the shared trial in the list study list
            if ($this->swRepo->totalRows($user) > 0) {
                $query->from('App\Entity\SharedWith', 'sw')
                    ->orWhere(
                        $query->expr()->andX(
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'spl.study = st.id',
                            'st.trial = tr.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
        }
        return $query->getQuery()->getResult();
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
        $query = $this->createQueryBuilder('spl')
            ->select("
                spl.id, spl.name as spl_name, germ.id as germ_id, germ.germplasmID, st.id as st_id, st.abbreviation as st_abbreviation,
                ds.id as ds_id, ds.name as ds_name, ae.id as ae_id, ae.name as ae_name"
                )
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Study', 'st')
            ->join('App\Entity\DevelopmentalStage', 'ds')
            ->join('App\Entity\AnatomicalEntity', 'ae')
            ->where('spl.isActive = 1')
            ->andWhere('spl.germplasm = germ.id')
            ->andWhere('spl.study = st.id')
            ->andWhere('spl.developmentalStage = ds.id')
            ->andWhere('spl.anatomicalEntity = ae.id');

        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('spl');
        $countQuery->select('COUNT(spl.id)')
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Study', 'st')
            ->join('App\Entity\DevelopmentalStage', 'ds')
            ->join('App\Entity\AnatomicalEntity', 'ae')
            ->where('spl.isActive = 1')
            ->andWhere('spl.germplasm = germ.id')
            ->andWhere('spl.study = st.id')
            ->andWhere('spl.developmentalStage = ds.id')
            ->andWhere('spl.anatomicalEntity = ae.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "spl.name like :filter",
                    "germ.germplasmID like :filter",
                    "st.abbreviation like :filter",
                    "ds.name like :filter",
                    "ae.name like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "spl.name like :filter",
                    "germ.germplasmID like :filter",
                    "st.abbreviation like :filter",
                    "ds.name like :filter",
                    "ae.name like :filter"
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
                if ($order['name'] == 'spl_name') {
                    $orderColumn = 'spl.name';
                }

                if ($order['name'] == 'germplasmID') {
                    $orderColumn = 'germ.germplasmID';
                }

                if ($order['name'] == 'st_abbreviation') {
                    $orderColumn = 'st.abbreviation';
                }

                if ($order['name'] == 'ds_name') {
                    $orderColumn = 'ds.name';
                }

                if ($order['name'] == 'ae_name') {
                    $orderColumn = 'ae.name';
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
    //  * @return Sample[] Returns an array of Sample objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sample
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
