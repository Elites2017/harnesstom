<?php

namespace App\Repository;

use App\Entity\CollectingMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectingMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectingMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectingMission[]    findAll()
 * @method CollectingMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectingMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectingMission::class);
    }

    // to show the number of accession by each collecting mission
    public function getAccessionsByCollectingMission() {
        $query = $this->createQueryBuilder('cm')
            ->select('cm.id as id, cm.name as name, count(cm.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cm.isActive = 1')
            ->andWhere('cm.id = accession.collmissid')
            ->groupBy('cm.id')
            ->orderBy('count(cm.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyColMission($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null,
                            $collectingSources = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('cm')
            ->select('cm.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cm.isActive = 1')
            ->andWhere('cm.id = accession.collmissid')
            ->groupBy('cm.id')
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
    //  * @return CollectingMission[] Returns an array of CollectingMission objects
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
    public function findOneBySomeField($value): ?CollectingMission
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
