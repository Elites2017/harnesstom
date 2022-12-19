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
    public function getAccessionFilteredOrNot(
        $countries = null, $biologicalStatuses = null, $selectedFruitWeightGrams = null,
        $selectedShapes = null, $selectedFasciation = null, $selectedShoulderShape = null,
        $selectedFColor = null, $selectedGreenSI = null, $selectedPuffinessA = null,
        $selectedPericarpT = null, $selectedFruitFirmness = null, $selectedBrix = null,
        $selectedLoad = null
        ) {
        $traitOntIds = [
            'ID:0000333', 'ID:0000390', 'ID:0000391','ID:0000393',
            'ID:0000395','ID:0000397','ID:0000402','ID:0000411',
            'SP:0000080','SP:0000165','SP:0000372'];

        $query = $this->createQueryBuilder('acc')
            ->from('App\Entity\Germplasm', 'germ')
            ->from('App\Entity\ObservationLevel', 'obsL')
            ->from('App\Entity\ObservationValue', 'obsVal')
            ->from('App\Entity\Scale', 'sc')
            ->from('App\Entity\ScaleCategory', 'scC')
            ->from('App\Entity\ObservationVariable', 'obsVar')
            ->from('App\Entity\TraitClass', 'trait')
            ->where('acc.isActive = 1')
            ->andWhere('trait.id = obsVar.variable')
            ->andWhere('sc.id = obsVar.scale')
            ->andWhere('sc.id = scC.scale')
            ->andWhere('obsVar.id = obsVal.observationVariable')
            ->andWhere('obsL.id = obsVal.observationLevel')
            ->andWhere('germ.id = obsL.germaplasm')
            ->andWhere('acc.id = germ.accession')
            ->andWhere('trait.ontology_id IN(:traitOntIds)')
        ->setParameter(':traitOntIds', array_values($traitOntIds));
        
        if ($countries) {
            $query->andWhere('acc.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('acc.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        if ($selectedFruitWeightGrams) {
            $query->andWhere('obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)')
            ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams));
        }
        if ($selectedShapes) {
            $query->andWhere('obsVal.shape_value IN(:selectedShapes)')
             ->setParameter(':selectedShapes', array_values($selectedShapes));
        }
        if ($selectedFasciation) {
            $query->andWhere('obsVal.fruit_fasciation_value IN(:selectedFasciation)')
             ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }
        if ($selectedShoulderShape) {
            $query->andWhere('obsVal.fruit_shoulder_value IN(:selectedShoulderShape)')
             ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape));
        }
        if ($selectedFColor) {
            $query->andWhere('obsVal.color_value IN(:selectedFColor)')
             ->setParameter(':selectedFColor', array_values($selectedFColor));
        }
        if ($selectedGreenSI) {
            $query->andWhere('obsVal.green_shoulder_value IN(:selectedGreenSI)')
             ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }
        if ($selectedPuffinessA) {
            $query->andWhere('obsVal.puffiness_value IN(:selectedPuffinessA)')
             ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }
        if ($selectedPericarpT) {
            $query->andWhere('obsVal.pericarp_thickness IN(:selectedPericarpT)')
             ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }
        if ($selectedFruitFirmness) {
            $query->andWhere('obsVal.fruit_firmness_value IN(:selectedFruitFirmness)')
             ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }
        if ($selectedBrix) {
            $query->andWhere('obsVal.brick_value IN(:selectedBrix)')
             ->setParameter(':selectedBrix', array_values($selectedBrix));
        }
        if ($selectedLoad) {
            $query->andWhere('obsVal.fruit_load_value IN(:selectedLoad)')
             ->setParameter(':selectedLoad', array_values($selectedLoad));
        }
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
