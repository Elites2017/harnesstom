<?php

namespace App\Repository;

use App\Entity\Accession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accession|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accession|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accession[]    findAll()
 * @method Accession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accession::class);
    }

    /**
     * 23/11/2022 3h20 PM
     * Returns all the accessions based on the filters if any (filters) 
     */
    public function getAccessionFilteredOrNot($countries = null, $biologicalStatuses = null, $selectedFruitWeightGrams = null) {
        $traitOntIds = [
            'ID:0000333', 'ID:0000390', 'ID:0000391','ID:0000393',
            'ID:0000395','ID:0000397','ID:0000402','ID:0000411',
            'SP:0000080','SP:0000165','SP:0000372'];

        $query = $this->createQueryBuilder('acc')
            ->from('App\Entity\Germplasm', 'germ')
            ->from('App\Entity\ObservationLevel', 'obsL')
            ->from('App\Entity\ObservationValue', 'obsVal')
            // ->join('App\Entity\Scale', 'sc')
            // ->join('App\Entity\ScaleCategory', 'scC')
            // ->join('App\Entity\Observationvariable', 'obsVar')
            // ->join('App\Entity\TraitClass', 'trait')
            ->where('acc.isActive = 1')
        //     ->andWhere('trait.id = obsVar.variable')
        //     ->andWhere('sc.id = obsVar.scale')
        //     ->andWhere('sc.id = scC.scale')
        //     ->andWhere('obsVar.id = obsVal.observationVariable')
            ->andWhere('obsL.id = obsVal.observationLevel')
            ->andWhere('germ.id = obsL.germaplasm')
            ->andWhere('acc.id = germ.accession');
        //     ->andWhere('trait.ontology_id IN(:traitOntIds)')
        // ->setParameter(':traitOntIds', array_values($traitOntIds));
        
        if ($countries) {
            $query->andWhere('acc.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('acc.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        // if ($selectedFruitWeightGrams) {
        //     $query->andWhere('obsVal.fruit_weight_value IN(:selectedselectedFruitWeightGrams)')
                
        //     ->setParameter(':selectedselectedFruitWeightGrams', array_values($selectedFruitWeightGrams));

        // }
        ;
        //dd($query->getDQL());
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Accession[] Returns an array of Accession objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Accession
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
