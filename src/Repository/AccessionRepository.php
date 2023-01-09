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
        if ($selectedFruitWeightGrams && !$selectedShapes && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)')
            ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams));
        }
        if ($selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.shape_value IN(:selectedShapes)')
             ->setParameter(':selectedShapes', array_values($selectedShapes));
        }
        if ($selectedFasciation && !$selectedFruitWeightGrams && !$selectedShapes && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.fruit_fasciation_value IN(:selectedFasciation)')
             ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }
        if ($selectedShoulderShape && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.fruit_shoulder_value IN(:selectedShoulderShape)')
             ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape));
        }
        if ($selectedFColor && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.color_value IN(:selectedFColor)')
             ->setParameter(':selectedFColor', array_values($selectedFColor));
        }
        if ($selectedGreenSI && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.green_shoulder_value IN(:selectedGreenSI)')
             ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }
        if ($selectedPuffinessA && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.puffiness_value IN(:selectedPuffinessA)')
             ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }
        if ($selectedPericarpT && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.pericarp_thickness IN(:selectedPericarpT)')
             ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }
        if ($selectedFruitFirmness && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedBrix && !$selectedLoad) {
            $query->andWhere('obsVal.fruit_firmness_value IN(:selectedFruitFirmness)')
             ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }
        if ($selectedBrix && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere('obsVal.brick_value IN(:selectedBrix)')
             ->setParameter(':selectedBrix', array_values($selectedBrix));
        }
        if ($selectedLoad && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
        !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
        !$selectedFruitFirmness && !$selectedBrix) {
            $query->andWhere('obsVal.fruit_load_value IN(:selectedLoad)')
             ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // $selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
        //     !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
        //     !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad

        if($selectedShapes && $selectedFruitWeightGrams) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)'))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
            //dd($query->getDQL());
            // SELECT * FROM `observation_value` WHERE shape_value in ('bell pepper') or fruit_fasciation_value in ('severe'); 
            // dd("Hola");
        }

        if($selectedShapes && $selectedFasciation) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedShoulderShape) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedFColor) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)'))
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedGreenSI) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedPuffinessA) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedPericarpT) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedFruitFirmness) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedBrix) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.brick_value IN(:selectedBrix)'))
                ->setParameter(':selectedBrix', array_values($selectedBrix))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'))
                ->setParameter(':selectedLoad', array_values($selectedLoad))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
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
