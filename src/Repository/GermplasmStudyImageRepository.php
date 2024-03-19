<?php

namespace App\Repository;

use App\Entity\GermplasmStudyImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GermplasmStudyImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GermplasmStudyImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GermplasmStudyImage[]    findAll()
 * @method GermplasmStudyImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GermplasmStudyImageRepository extends ServiceEntityRepository
{
    private $swRepo;

    public function __construct(ManagerRegistry $registry, SharedWithRepository $swRepo)
    {
        $this->swRepo = $swRepo;
        parent::__construct($registry, GermplasmStudyImage::class);
    }

    public function findReleasedTrialStudyGermplasmStudyImage($user = null)
    {
        // MySQL format
        $currentDate = date('Y-m-d');
        $currentDate = new \DateTime($currentDate);
        $query = $this->createQueryBuilder('gmpStImg')
            ->from('App\Entity\Study', 'st')    
            ->from('App\Entity\Trial', 'tr')
            ->Where('gmpStImg.isActive = 1')
            ->Where('gmpStImg.StudyID = st.id')
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
                            'gmpStImg.StudyID = st.id',
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
                            'gmpStImg.StudyID = st.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate',
                            'sw.user = :user',
                            'sw.trial = tr.id',
                            ))
                    ->orWhere(
                        $query->expr()->andX(
                            'tr.createdBy = :user',
                            'gmpStImg.StudyID = st.id',
                            'st.trial = tr.id',
                            'st.trial = tr.id',
                            'tr.publicReleaseDate >= :currentDate'))
                        ->setParameter(':user', $user->getId())
                        ->setParameter(':currentDate', $currentDate);
            }
        }
        return $query->getQuery()->getResult();
    }    

    public function getTotalRows() {
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
        $query = $this->createQueryBuilder('gsti')
            ->select("
                gsti.id, germ.id as germ_id, germ.germplasmID as germplasmID, st.id as study_id, st.abbreviation as study_abbreviation, description,
                ft.id as factor_id, ft.name as factor_name, ds.id as dev_stage_id, ds.name as dev_stage_name, ae.id as ae_id, ae.name as ae_name, filename"
                )
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Study', 'st')
            ->join('App\Entity\FactorType', 'ft')
            ->join('App\Entity\DevelomentalStage', 'ds')
            ->join('App\Entity\AnatomicalEntity', 'ae')
            ->where('gsti.isActive = 1')
            ->andWhere('gsti.GermplasmID = germ.id')
            ->andWhere('gsti.StudyID = st.id')
            ->andWhere('gsti.factor = ft.id')
            ->andWhere('gsti.developmentStage = ds.id')
            ->andWhere('gsti.pnatomicalEntity = ae.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('obsL');
        $countQuery->select('COUNT(obsL.id)')
            ->join('App\Entity\Germplasm', 'germ')
            ->join('App\Entity\Study', 'st')
            ->join('App\Entity\FactorType', 'ft')
            ->join('App\Entity\DevelomentalStage', 'ds')
            ->join('App\Entity\AnatomicalEntity', 'ae')
            ->where('gsti.isActive = 1')
            ->andWhere('gsti.GermplasmID = germ.id')
            ->andWhere('gsti.StudyID = st.id')
            ->andWhere('gsti.factor = ft.id')
            ->andWhere('gsti.developmentStage = ds.id')
            ->andWhere('gsti.pnatomicalEntity = ae.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "germ.germplasmID like :filter",
                    "st.abbreviation like :filter",
                    "ft.name like :filter",
                    "ds.name like :filter",
                    "ae.name like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "germ.germplasmID like :filter",
                    "st.abbreviation like :filter",
                    "ft.name like :filter",
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
                if ($order['name'] == 'germplasmID') {
                    $orderColumn = 'germ.germplasmID';
                }

                if ($order['name'] == 'study_abbreviation') {
                    $orderColumn = 'st.abbreviation';
                }

                if ($order['name'] == 'factor_name') {
                    $orderColumn = 'ft.name';
                }

                if ($order['name'] == 'dev_stage_name') {
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
    //  * @return GermplasmStudyImage[] Returns an array of GermplasmStudyImage objects
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
    public function findOneBySomeField($value): ?GermplasmStudyImage
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
