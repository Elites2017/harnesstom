<?php

namespace App\Repository;

use App\Entity\Study;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SharedWithRepository;

/**
 * @method Study|null find($id, $lockMode = null, $lockVersion = null)
 * @method Study|null findOneBy(array $criteria, array $orderBy = null)
 * @method Study[]    findAll()
 * @method Study[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudyRepository extends ServiceEntityRepository
{
    private $swRepo;
    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, Study::class);
    }

    public function findReleasedTrialStudy($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('st')
            ->from('App\Entity\Trial', 'tr')
            ->Where('st.isActive = 1')
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
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
        }
        return $query->getQuery()->getResult();
    }

    public function getStudyObsLevels($study) {
        $query = $this->createQueryBuilder('study')
            ->join('App\Entity\ObservationLevel', 'obsLevel')
            ->where('study.isActive = 1')
            ->andWhere('study.id = obsLevel.study')
        ;
        return $query->getQuery()->getResult();
    }

    public function getStudyObervations() {
        $query = $this->createQueryBuilder('study')
            ->join('App\Entity\ObservationLevel', 'obsLevel')
            ->join('App\Entity\ObservationValueOriginal', 'obsVal')
            ->where('study.isActive = 1')
            ->andWhere('study.id = obsLevel.study')
            ->andWhere('obsLevel.id = obsVal.unitName')
        ;

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
        $query = $this->createQueryBuilder('st')
            ->select("
                st.id as st_id, st.abbreviation as st_abbreviation, st.name as st_name, tr.id as tr_id,
                tr.abbreviation as tr_abbreviation, gf.id as gf_id, gf.name as gf_name"
                )
            ->join('App\Entity\Trial', 'tr')
            ->join('App\Entity\GrowthFacilityType', 'gf')
            ->where('st.isActive = 1')
            ->andWhere('st.trial = tr.id')
            ->andWhere('st.growthFacility = gf.id');

        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('st');
        $countQuery->select('COUNT(st.id)')
            ->join('App\Entity\Trial', 'tr')
            ->join('App\Entity\GrowthFacilityType', 'gf')
            ->where('st.isActive = 1')
            ->andWhere('st.trial = tr.id')
            ->andWhere('st.growthFacility = gf.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "st.name like :filter",
                    "st.abbreviation like :filter",
                    "tr.abbreviation like :filter",
                    "gf.name like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "st.name like :filter",
                    "st.abbreviation like :filter",
                    "tr.abbreviation like :filter",
                    "gf.name like :filter"
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
                if ($order['name'] == 'st_name') {
                    $orderColumn = 'st.name';
                }

                if ($order['name'] == 'st_abbreviation') {
                    $orderColumn = 'st.abbreviation';
                }

                if ($order['name'] == 'tr_abbreviation') {
                    $orderColumn = 'tr.abbreviation';
                }

                if ($order['name'] == 'gf_name') {
                    $orderColumn = 'gf.name';
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
    //  * @return Study[] Returns an array of Study objects
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
    public function findOneBySomeField($value): ?Study
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
