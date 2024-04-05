<?php

namespace App\Repository;

use App\Entity\ObservationValueOriginal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ObservationValueOriginal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationValueOriginal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationValueOriginal[]    findAll()
 * @method ObservationValueOriginal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationValueOriginalRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, ObservationValueOriginal::class);
    }

    public function findReleasedTrialStudyObsLevelObsValues($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('obsValOri')
            ->from('App\Entity\ObservationLevel', 'obsL')    
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('obsValOri.isActive = 1')
            ->andWhere('obsValOri.unitName = obsL.id')
            ->andWhere('obsL.study = st.id')
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
                            'obsValOri.unitName = obsL.id',
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
                            'obsValOri.unitName = obsL.id',
                            'obsL.study = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'obsValOri.unitName = obsL.id',
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

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('obsVO')
            ->select('count(obsVO.id)')
            ->where('obsVO.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('obsVO')
            ->select("
                obsVO.id, obsL.id as obsL_id, obsL.unitname as obsL_unitname, obsV.id as obsV_id, obsV.name as obsV_name, obsVO.value as value"
                )
            ->join('App\Entity\ObservationLevel', 'obsL')
            ->join('App\Entity\ObservationVariable', 'obsV')
            ->where('obsVO.isActive = 1')
            ->andWhere('obsVO.unitName = obsL.id')
            ->andWhere('obsVO.observationVariableOriginal = obsV.id');
            
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('obsVO');
        $countQuery->select('COUNT(obsVO.id)')
            ->join('App\Entity\ObservationLevel', 'obsL')
            ->join('App\Entity\ObservationVariable', 'obsV')
            ->where('obsVO.isActive = 1')
            ->andWhere('obsVO.unitName = obsL.id')
            ->andWhere('obsVO.observationVariableOriginal = obsV.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "obsL.unitname like :filter",
                    "obsV.name like :filter",
                    "obsVO.value like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "obsL.unitname like :filter",
                    "obsV.name like :filter",
                    "obsVO.value like :filter"
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
                if ($order['name'] == 'obsL_unitname') {
                    $orderColumn = 'obsL.unitname';
                }

                if ($order['name'] == 'obsV_name') {
                    $orderColumn = 'obsV.name';
                }

                if ($order['name'] == 'value') {
                    $orderColumn = 'obsVO.value';
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
    //  * @return ObservationValueOriginal[] Returns an array of ObservationValueOriginal objects
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
    public function findOneBySomeField($value): ?ObservationValueOriginal
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
