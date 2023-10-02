<?php

namespace App\Repository;

use App\Entity\Institute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institute[]    findAll()
 * @method Institute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstituteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institute::class);
    }

    // Get the total number of elements
    public function totalRows() {
        return $this->createQueryBuilder('inst')
            ->select('count(inst.id)')
            ->where('inst.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // to show the number of accession by maintaining institute
    public function getAccessionsByMaintainingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, inst.acronym as acronym, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.instcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by donor institute
    public function getAccessionsByDonorInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, inst.acronym as acronym, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.donorcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by breding institute
    public function getAccessionsByBreedingInstitute() {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, inst.acronym as acronym, count(inst.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.bredcode')
            ->groupBy('inst.id')
            ->orderBy('count(inst.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each object list
    public function getAccessionQtyMainInstitute($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null, $collectingMissions = null,
                                    $collectingSources = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.instcode')
            ->groupBy('inst.id')
            ->orderBy('count(accession.id)', 'DESC')
        ;

        if ($countries) {
            $query->andWhere('accession.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('accession.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        if ($mlsStatuses) {
            $query->andWhere('accession.mlsStatus IN(:selectedMLSStatuses)')
            ->setParameter(':selectedMLSStatuses', array_values($mlsStatuses));
        }
        if ($taxonomies) {
            $query->andWhere('accession.taxon IN(:selectedTaxonomies)')
            ->setParameter(':selectedTaxonomies', array_values($taxonomies));
        }
        if ($collectingMissions) {
                $query->andWhere('accession.collmissid IN(:selectedCollectingMissions)')
                ->setParameter(':selectedCollectingMissions', array_values($collectingMissions));
        }
        if ($collectingSources) {
                $query->andWhere('accession.collsrc IN(:selectedCollectingSources)')
                ->setParameter(':selectedCollectingSources', array_values($collectingSources));
        }
        if ($donorInstitutes) {
                $query->andWhere('accession.donorcode IN(:selectedDonorInstitutes)')
                ->setParameter(':selectedDonorInstitutes', array_values($donorInstitutes));
        }
        if ($breedingInstitutes) {
                $query->andWhere('accession.bredcode IN(:selectedBreedingInstitutes)')
                ->setParameter(':selectedBreedingInstitutes', array_values($breedingInstitutes));
        }
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each object list
    public function getAccessionQtyDonorInstitute($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null, $collectingMissions = null,
                                    $collectingSources = null, $maintainingInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.donorcode')
            ->groupBy('inst.id')
            ->orderBy('count(accession.id)', 'DESC')
        ;

        if ($countries) {
            $query->andWhere('accession.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('accession.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        if ($mlsStatuses) {
            $query->andWhere('accession.mlsStatus IN(:selectedMLSStatuses)')
            ->setParameter(':selectedMLSStatuses', array_values($mlsStatuses));
        }
        if ($taxonomies) {
            $query->andWhere('accession.taxon IN(:selectedTaxonomies)')
            ->setParameter(':selectedTaxonomies', array_values($taxonomies));
        }
        if ($collectingMissions) {
                $query->andWhere('accession.collmissid IN(:selectedCollectingMissions)')
                ->setParameter(':selectedCollectingMissions', array_values($collectingMissions));
        }
        if ($collectingSources) {
                $query->andWhere('accession.collsrc IN(:selectedCollectingSources)')
                ->setParameter(':selectedCollectingSources', array_values($collectingSources));
        }
        if ($maintainingInstitutes) {
                $query->andWhere('accession.instcode IN(:selectedMaintainingInstitutes)')
                ->setParameter(':selectedMaintainingInstitutes', array_values($maintainingInstitutes));
        }
        if ($breedingInstitutes) {
                $query->andWhere('accession.bredcode IN(:selectedBreedingInstitutes)')
                ->setParameter(':selectedBreedingInstitutes', array_values($breedingInstitutes));
        }
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each object list
    public function getAccessionQtyBredInstitute($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null, $collectingMissions = null,
                                    $collectingSources = null, $maintainingInstitutes =  null, $donorInstitutes = null) {
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('inst.isActive = 1')
            ->andWhere('inst.id = accession.bredcode')
            ->groupBy('inst.id')
            ->orderBy('count(accession.id)', 'DESC')
        ;

        if ($countries) {
            $query->andWhere('accession.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('accession.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        if ($mlsStatuses) {
            $query->andWhere('accession.mlsStatus IN(:selectedMLSStatuses)')
            ->setParameter(':selectedMLSStatuses', array_values($mlsStatuses));
        }
        if ($taxonomies) {
            $query->andWhere('accession.taxon IN(:selectedTaxonomies)')
            ->setParameter(':selectedTaxonomies', array_values($taxonomies));
        }
        if ($collectingMissions) {
                $query->andWhere('accession.collmissid IN(:selectedCollectingMissions)')
                ->setParameter(':selectedCollectingMissions', array_values($collectingMissions));
        }
        if ($collectingSources) {
                $query->andWhere('accession.collsrc IN(:selectedCollectingSources)')
                ->setParameter(':selectedCollectingSources', array_values($collectingSources));
        }
        if ($maintainingInstitutes) {
                $query->andWhere('accession.instcode IN(:selectedMaintainingInstitutes)')
                ->setParameter(':selectedMaintainingInstitutes', array_values($maintainingInstitutes));
        }
        if ($donorInstitutes) {
                $query->andWhere('accession.donorcode IN(:selectedDonorInstitutes)')
                ->setParameter(':selectedDonorInstitutes', array_values($donorInstitutes));
        }
        return $query->getQuery()->getArrayResult();
    }


    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('inst')
            ->select('inst.id, inst.instcode, inst.acronym, inst.name, ctry.id as country_id, ctry.iso3 as country_iso3')
            ->join('App\Entity\Country', 'ctry')
            ->where('inst.isActive = 1')
            ->andWhere('inst.country = ctry.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('inst');
        $countQuery->select('COUNT(inst.id)')
            ->join('App\Entity\Country', 'ctry')
            ->where('inst.isActive = 1')
            ->andWhere('inst.country = ctry.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "inst.acronym like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                    "inst.instcode like :filter",
                    "inst.name like :filter",
                    "inst.acronym like :filter"
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
                if ($order['name'] == 'instcode') {
                    $orderColumn = 'inst.instcode';
                }

                if ($order['name'] == 'acronym') {
                    $orderColumn = 'inst.acronym';
                }

                if ($order['name'] == 'name') {
                    $orderColumn = 'inst.name';
                }

                if ($order['name'] == 'country_iso3') {
                    $orderColumn = 'ctry.iso3';
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
    //  * @return Institute[] Returns an array of Institute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Institute
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
