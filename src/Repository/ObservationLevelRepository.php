<?php

namespace App\Repository;

use App\Entity\ObservationLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationLevel[]    findAll()
 * @method ObservationLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationLevelRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, ObservationLevel::class);
    }

    // to download publicly release trial associated data
    public function getPublicReleasedData()
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('obsL')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('obsL.study = st.id')
            ->andWhere('st.trial = tr.id')
            ->andWhere('tr.publicReleaseDate <= :currentDate')
            ->setParameter(':currentDate', $currentDate)
        ;
        return $query->getQuery()->getResult();
    }

    public function findReleasedTrialStudyObsLevel($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('obsL')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('obsL.isActive = 1')
            ->Where('obsL.study = st.id')
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
                            'obsL.study = st.id',
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
                            'obsL.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'obsL.study = st.id',
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
        $query = $this->createQueryBuilder('obsL')
            ->select("
                germ.id as germ_id, germ.germplasmID, obsL.id, obsL.unitname, obsL.name, obsL.blockNumber, obsL.subBlockNumber, obsL.plotNumber,
                obsL.plantNumber, obsL.replicate, tr.id as tr_id, tr.abbreviation as tr_abbreviation, st.id as st_id, st.abbreviation as st_abbreviation"
                )
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Trial', 'tr')
            ->join('App\Entity\Study', 'st')
            ->where('germ.isActive = 1')
            ->andWhere('obsL.germplasm = germ.id')
            ->andWhere('obsL.study = st.id')
            ->andWhere('st.trial = tr.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('obsL');
        $countQuery->select('COUNT(obsL.id)')
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Trial', 'tr')
            ->join('App\Entity\Study', 'st')
            ->where('germ.isActive = 1')
            ->andWhere('obsL.germplasm = germ.id')
            ->andWhere('obsL.study = st.id')
            ->andWhere('st.trial = tr.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "germ.germplasmID like :filter",
                    "obsL.unitname like :filter",
                    "obsL.name like :filter",
                    "obsL.blockNumber like :filter",
                    "obsL.subBlockNumber like :filter",
                    "obsL.plotNumber like :filter",
                    "obsL.plantNumber like :filter",
                    "obsL.replicate like :filter",
                    "tr.abbreviation like :filter",
                    "st.abbreviation like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "germ.germplasmID like :filter",
                    "obsL.unitname like :filter",
                    "obsL.name like :filter",
                    "obsL.blockNumber like :filter",
                    "obsL.subBlockNumber like :filter",
                    "obsL.plotNumber like :filter",
                    "obsL.plantNumber like :filter",
                    "obsL.replicate like :filter",
                    "tr.abbreviation like :filter",
                    "st.abbreviation like :filter"
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

                if ($order['name'] == 'unitname') {
                    $orderColumn = 'obsL.unitname';
                }

                if ($order['name'] == 'name') {
                    $orderColumn = 'obsL.name';
                }

                if ($order['name'] == 'blockNumber') {
                    $orderColumn = 'obsL.blockNumber';
                }

                if ($order['name'] == 'subBlockNumber') {
                    $orderColumn = 'obsL.subBlockNumber';
                }

                if ($order['name'] == 'plotNumber') {
                    $orderColumn = 'obsL.plotNumber';
                }

                if ($order['name'] == 'plantNumber') {
                    $orderColumn = 'obsL.plantNumber';
                }

                if ($order['name'] == 'replicate') {
                    $orderColumn = 'obsL.replicate';
                }

                if ($order['name'] == 'tr_abbreviation') {
                    $orderColumn = 'tr.abbreviation';
                }

                if ($order['name'] == 'st_abbreviation') {
                    $orderColumn = 'st.abbreviation';
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
    //  * @return ObservationLevel[] Returns an array of ObservationLevel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ObservationLevel
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
