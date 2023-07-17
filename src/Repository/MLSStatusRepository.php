<?php

namespace App\Repository;

use App\Entity\MLSStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MLSStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MLSStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MLSStatus[]    findAll()
 * @method MLSStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MLSStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MLSStatus::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each mls status
    public function getAccessionsByMLSStatus() {
        $query = $this->createQueryBuilder('mlsS')
            ->select('mlsS.id as id, mlsS.name as name, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('mlsS.isActive = 1')
            ->andWhere('mlsS.id = accession.mlsStatus')
            ->groupBy('mlsS.id')
            ->orderBy('count(mlsS.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyMLSStatus($countries = null, $biologicalStatuses = null, $taxonomies = null, $collectingMissions = null,
                            $collectingSources = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('mlsS')
            ->select('mlsS.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('mlsS.isActive = 1')
            ->andWhere('mlsS.id = accession.mlsStatus')
            ->groupBy('mlsS.id')
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

    // /**
    //  * @return MLSStatus[] Returns an array of MLSStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MLSStatus
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
