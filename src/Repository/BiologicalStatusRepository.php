<?php

namespace App\Repository;

use App\Entity\BiologicalStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiologicalStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiologicalStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiologicalStatus[]    findAll()
 * @method BiologicalStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiologicalStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BiologicalStatus::class);
    }

    public function getAccessionBiologicalStatuses() {
        $query = $this->createQueryBuilder('biologicalStatus')
            ->join('App\Entity\Accession', 'accession')
            ->where('biologicalStatus.isActive = 1')
            ->andWhere('biologicalStatus.id = accession.sampstat')
        ;
        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each biological status
    public function getAccessionsByBiologicalStatus() {
        $query = $this->createQueryBuilder('biologicalStatus')
            ->select('biologicalStatus.id, biologicalStatus.name, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('biologicalStatus.isActive = 1')
            ->andWhere('biologicalStatus.id = accession.sampstat')
            ->groupBy('biologicalStatus.id')
            ->orderBy('count(biologicalStatus.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyBiologicalStatus($countries = null, $mlsStatuses = null, $taxonomies = null, $collectingMissions = null,
                            $collectingSources = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('biologicalStatus')
            ->select('biologicalStatus.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('biologicalStatus.isActive = 1')
            ->andWhere('biologicalStatus.id = accession.sampstat')
            ->groupBy('biologicalStatus.id')
            ->orderBy('count(accession.id)', 'DESC')
        ;
        if ($countries) {
            $query->andWhere('accession.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
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

    // get only the parents
    public function getParentOnlyBiologicalStatus()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return BiologicalStatus[] Returns an array of BiologicalStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BiologicalStatus
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
