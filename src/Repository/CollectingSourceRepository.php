<?php

namespace App\Repository;

use App\Entity\CollectingSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CollectingSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectingSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectingSource[]    findAll()
 * @method CollectingSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectingSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectingSource::class);
    }

    // get only the parents
    public function getParentsOnly()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.parentTerm IS NULL');

        return $query->getQuery()->getResult();
    }

    // to show the number of accession by each collecting source
    public function getAccessionsByCollectingSource() {
        $query = $this->createQueryBuilder('cs')
            ->select('cs.id as id, cs.name as name, count(cs.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cs.isActive = 1')
            ->andWhere('cs.id = accession.collsrc')
            ->groupBy('cs.id')
            ->orderBy('count(cs.id)', 'DESC')
        ;
        return $query->getQuery()->getArrayResult();
    }

    // to show ion the right side of each country
    public function getAccessionQtyColSource($countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null,
                            $collectingMissions = null, $maintainingInstitutes = null, $donorInstitutes =  null, $breedingInstitutes = null) {
        $query = $this->createQueryBuilder('cm')
            ->select('cm.id as id, count(accession.id) as accQty')
            ->join('App\Entity\Accession', 'accession')
            ->where('cm.isActive = 1')
            ->andWhere('cm.id = accession.collsrc')
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
        if ($collectingMissions) {
                $query->andWhere('accession.collmissid IN(:selectedCollectingMissions)')
                ->setParameter(':selectedCollectingMissions', array_values($collectingMissions));
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
    //  * @return CollectingSource[] Returns an array of CollectingSource objects
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
    public function findOneBySomeField($value): ?CollectingSource
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
