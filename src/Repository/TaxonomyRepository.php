<?php

namespace App\Repository;

use App\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taxonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomy[]    findAll()
 * @method Taxonomy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxonomy::class);
    }

    // to show the number of accession by each taxonomy
    public function getAccessionsByTaxonomy() {
        $query = $this->createQueryBuilder('tax')
            ->select('tax.id as id, tax.taxonid as taxonid, count(tax.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('tax.isActive = 1')
            ->andWhere('tax.id = accession.taxon')
            ->groupBy('tax.id')
            ->orderBy('count(tax.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyTaxonomy($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $collectingMissions = null,
                            $collectingSources = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('tax')
            ->select('tax.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('tax.isActive = 1')
            ->andWhere('tax.id = accession.taxon')
            ->groupBy('tax.id')
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

    // to show the number of accession by species
    // public function getAccessionsBySpecies() {
    //     $query = $this->createQueryBuilder('tax')
    //         ->select('tax.species as species, tax.id as id, count(tax.id) as accQty')
    //         ->join('App\Entity\Accession', 'accession')
    //         ->where('tax.isActive = 1')
    //         ->andWhere('tax.id = accession.taxon')
    //         ->groupBy('tax.id')
    //         ->orderBy('count(tax.id)', 'DESC')
    //     ;
    //     return $query->getQuery()->getResult();
    // }

    // /**
    //  * @return Taxonomy[] Returns an array of Taxonomy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taxonomy
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
