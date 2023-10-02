<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }
    
    public function getAccessionCountries() {
        $query = $this->createQueryBuilder('country')
            ->select('country.id as id, country.iso3, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('country.isActive = 1')
            ->andWhere('country.id = accession.origcty')
            ->groupBy('country.id')
            ->orderBy('count(country.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyCountry($biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null, $collectingMissions = null,
                                    $collectingSources = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('country')
            ->select('country.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('country.isActive = 1')
            ->andWhere('country.id = accession.origcty')
            ->groupBy('country.id')
            ->orderBy('count(accession.id)', 'DESC')
        ;
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
        if ($breedingInstitutes) {
                $query->andWhere('accession.bredcode IN(:selectedBreedingInstitutes)')
                ->setParameter(':selectedBreedingInstitutes', array_values($breedingInstitutes));
        }
        return $query->getQuery()->getArrayResult();
    }

    // to show the number of accession by each country
    // public function getAccessionsByCountry($biologicalStatuses = null) {
    //     $query = $this->createQueryBuilder('ctry')
    //         ->select('ctry as country, count(ctry.id) as accQty')
    //         ->join('App\Entity\Accession', 'accession')
    //         ->where('ctry.isActive = 1')
    //         ->andWhere('ctry.id = accession.origcty')
    //         ->groupBy('ctry.id')
    //         ->orderBy('count(ctry.id)', 'DESC')
    //     ;
    //     if ($biologicalStatuses) {
    //         $query->andWhere('accession.sampstat IN(:selectedBiologicalStatuses)')
    //         ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
    //     }
    //     return $query->getQuery()->getResult();
    // }

    // /**
    //  * @return Country[] Returns an array of Country objects
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
    public function findOneBySomeField($value): ?Country
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
