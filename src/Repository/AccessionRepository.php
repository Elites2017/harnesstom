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
            ->andWhere('germ.id = obsL.germplasm')
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
            $query->andWhere('obsVal.brix_value IN(:selectedBrix)')
             ->setParameter(':selectedBrix', array_values($selectedBrix));
        }
        if ($selectedLoad && !$selectedShapes && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
        !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
        !$selectedFruitFirmness && !$selectedBrix) {
            $query->andWhere('obsVal.fruit_load_value IN(:selectedLoad)')
             ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // 2 pairs
        if($selectedShapes && $selectedFruitWeightGrams && !$selectedBrix && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)'))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedFasciation && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedShoulderShape && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedFColor&& !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT && !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)'))
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedGreenSI && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedPuffinessA && !$selectedPericarpT && !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedPuffinessA && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPericarpT && !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedPericarpT && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedFruitFirmness && !$selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedBrix && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.brix_value IN(:selectedBrix)'))
                ->setParameter(':selectedBrix', array_values($selectedBrix))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        if($selectedShapes && $selectedLoad && !$selectedFruitWeightGrams && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'))
                ->setParameter(':selectedLoad', array_values($selectedLoad))
                ->setParameter(':selectedShapes', array_values($selectedShapes));
        }

        // 3 pairs
        if(($selectedShapes && $selectedFruitWeightGrams) && $selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && $selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            $selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.color_value IN(:selectedFColor)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFColor', array_values($selectedFColor));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFruitWeightGrams) && !$selectedFasciation && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 1
        if(($selectedShapes && $selectedFasciation) && $selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && $selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            $selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.color_value IN(:selectedFColor)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFasciation) && !$selectedFruitWeightGrams && !$selectedShoulderShape &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 2
        if(($selectedShapes && $selectedShoulderShape) && $selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && $selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            $selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.color_value IN(:selectedFColor)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedShoulderShape) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedFColor && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 3
        if(($selectedShapes && $selectedFColor) && $selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFColor', array_values($selectedFColor));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && $selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            $selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFColor', array_values($selectedFColor));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor) && !$selectedFruitWeightGrams && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 3.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && $selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)'
                            )
                        )
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            $selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFColor', array_values($selectedFColor));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams) && !$selectedFasciation &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 4.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            $selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)'
                            )
                        )
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedFColor', array_values($selectedFColor));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation) &&
            !$selectedShoulderShape && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 5.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && $selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && !$selectedGreenSI && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && !$selectedGreenSI && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape) && !$selectedGreenSI && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 6.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI) && $selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI) && !$selectedPuffinessA && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI) && !$selectedPuffinessA && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI) && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI) && !$selectedPuffinessA && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 7.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA) && $selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA) && !$selectedPericarpT &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA) && !$selectedPericarpT &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA) && !$selectedPericarpT &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 8.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT) &&
            $selectedFruitFirmness && !$selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT) &&
            !$selectedFruitFirmness && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT) &&
            !$selectedFruitFirmness && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 9.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT &&
            $selectedFruitFirmness) && $selectedBrix && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)',
                            'obsVal.brix_value IN(:selectedBrix)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT &&
            $selectedFruitFirmness) && !$selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        // change fixed couple 10.1
        if(($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT &&
            $selectedFruitFirmness && $selectedBrix) && !$selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)',
                            'obsVal.brix_value IN(:selectedBrix)',
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedBrix', array_values($selectedBrix));
        }

        // change fixed couple 11.1 - ALL
        if($selectedShapes && $selectedFColor && $selectedFruitWeightGrams && $selectedFasciation &&
            $selectedShoulderShape && $selectedGreenSI && $selectedPuffinessA && $selectedPericarpT &&
            $selectedFruitFirmness && $selectedBrix && $selectedLoad) {
            $query->andWhere(
                    $query->expr()->orX(
                            'obsVal.shape_value IN(:selectedShapes)',
                            'obsVal.color_value IN(:selectedFColor)',
                            'obsVal.fruit_weight_value IN(:selectedFruitWeightGrams)',
                            'obsVal.fruit_shoulder_value IN(:selectedShoulderShape)',
                            'obsVal.fruit_fasciation_value IN(:selectedFasciation)',
                            'obsVal.green_shoulder_value IN(:selectedGreenSI)',
                            'obsVal.puffiness_value IN(:selectedPuffinessA)',
                            'obsVal.pericarp_thickness IN(:selectedPericarpT)',
                            'obsVal.fruit_firmness_value IN(:selectedFruitFirmness)',
                            'obsVal.brix_value IN(:selectedBrix)',
                            'obsVal.fruit_load_value IN(:selectedLoad)'
                            )
                        )
                ->setParameter(':selectedFColor', array_values($selectedFColor))
                ->setParameter(':selectedShoulderShape', array_values($selectedShoulderShape))
                ->setParameter(':selectedShapes', array_values($selectedShapes))
                ->setParameter(':selectedFruitWeightGrams', array_values($selectedFruitWeightGrams))
                ->setParameter(':selectedFasciation', array_values($selectedFasciation))
                ->setParameter(':selectedGreenSI', array_values($selectedGreenSI))
                ->setParameter(':selectedPuffinessA', array_values($selectedPuffinessA))
                ->setParameter(':selectedPericarpT', array_values($selectedPericarpT))
                ->setParameter(':selectedFruitFirmness', array_values($selectedFruitFirmness))
                ->setParameter(':selectedBrix', array_values($selectedBrix))
                ->setParameter(':selectedLoad', array_values($selectedLoad));
        }

        ;
        //dd($query->getDQL());
        return $query->getQuery()->getResult();
    }

    public function totalRows() {
        return $this->createQueryBuilder('tab')
            ->select('count(tab.id)')
            ->where('tab.isActive = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAccessionAdvancedSearch(
        $countries = null, $biologicalStatuses = null, $mlsStatuses = null, $taxonomies = null,
        $collectingMissions = null, $collectingSources = null, $selectedMaintainingInstitutes = null,
        $selectedDonorInstitutes =null, $selectedBreedingInstitutes = null) {
        
        $query = $this->createQueryBuilder('acc')
            ->where('acc.isActive = 1');
        
        if ($countries) {
            $query->andWhere('acc.origcty IN(:selectedCountries)')
            ->setParameter(':selectedCountries', array_values($countries));
        }
        if ($biologicalStatuses) {
            $query->andWhere('acc.sampstat IN(:selectedBiologicalStatuses)')
            ->setParameter(':selectedBiologicalStatuses', array_values($biologicalStatuses));
        }
        if ($mlsStatuses) {
                $query->andWhere('acc.mlsStatus IN(:selectedMLSStatuses)')
                ->setParameter(':selectedMLSStatuses', array_values($mlsStatuses));
        }
        if ($taxonomies) {
                $query->andWhere('acc.taxon IN(:selectedTaxonomies)')
                ->setParameter(':selectedTaxonomies', array_values($taxonomies));
        }
        if ($collectingMissions) {
                $query->andWhere('acc.collmissid IN(:selectedCollectingMissions)')
                ->setParameter(':selectedCollectingMissions', array_values($collectingMissions));
        }
        if ($collectingSources) {
                $query->andWhere('acc.collsrc IN(:selectedCollectingSources)')
                ->setParameter(':selectedCollectingSources', array_values($collectingSources));
        }
        if ($selectedMaintainingInstitutes) {
                $query->andWhere('acc.instcode IN(:selectedMaintainingInstitutes)')
                ->setParameter(':selectedMaintainingInstitutes', array_values($selectedMaintainingInstitutes));
        }
        if ($selectedDonorInstitutes) {
                $query->andWhere('acc.donorcode IN(:selectedDonorInstitutes)')
                ->setParameter(':selectedDonorInstitutes', array_values($selectedDonorInstitutes));
        }
        if ($selectedBreedingInstitutes) {
                $query->andWhere('acc.bredcode IN(:selectedBreedingInstitutes)')
                ->setParameter(':selectedBreedingInstitutes', array_values($selectedBreedingInstitutes));
        }
        ;
        return $query->getQuery()->getResult();
    }

    // for bootstrap datatable server-side processing
    public function getObjectsList($start, $length, $orders, $search, $columns)
    {
        // Create Main Query
        $query = $this->createQueryBuilder('acc')
            ->select('
                acc.id, acc.accenumb, acc.accename, acc.puid, ctry.id as country_id, ctry.iso3 as country_iso3,
                bs.id as bs_id, bs.name as bs_name, mls.id as mls_id, mls.name as mls_name, inst.id as inst_id,
                inst.instcode as inst_instcode, acc.maintainernumb, tx.id as tx_id, tx.taxonid as taxonid'
                )
                ->join('App\Entity\Country', 'ctry')
                ->join('App\Entity\BiologicalStatus', 'bs')
                ->join('App\Entity\MLSStatus', 'mls')
                ->join('App\Entity\Institute', 'inst')
                ->join('App\Entity\Taxonomy', 'tx')
                ->where('acc.isActive = 1')
                ->andWhere('acc.sampstat = bs.id')
                ->andWhere('acc.mlsStatus = mls.id')
                ->andWhere('acc.origcty = ctry.id')
                ->andWhere('acc.taxon = tx.id')
                ->andWhere('acc.instcode = inst.id');
        
        // Create Count Query
        $countQuery = $this->createQueryBuilder('acc');
        $countQuery->select('COUNT(acc.id)')
                ->join('App\Entity\Country', 'ctry')
                ->join('App\Entity\BiologicalStatus', 'bs')
                ->join('App\Entity\MLSStatus', 'mls')
                ->join('App\Entity\Institute', 'inst')
                ->join('App\Entity\Taxonomy', 'tx')
                ->where('acc.isActive = 1')
                ->andWhere('acc.sampstat = bs.id')
                ->andWhere('acc.mlsStatus = mls.id')
                ->andWhere('acc.origcty = ctry.id')
                ->andWhere('acc.taxon = tx.id')
                ->andWhere('acc.instcode = inst.id');
        
        if ($search["filter"] != null) {
            $query->andWhere(
                $query->expr()->orX(
                    "tx.taxonid like :filter",
                    "acc.accenumb like :filter",
                    "acc.accename like :filter",
                    "acc.puid like :filter",
                    "ctry.iso3 like :filter",
                    "bs.name like :filter",
                    "mls.name like :filter",
                    "inst.instcode like :filter",
                    "acc.maintainernumb like :filter"
                    )
            )
            ->setParameter('filter', "%".$search['filter']."%")
            ;

            $countQuery->andWhere(
                $countQuery->expr()->orX(
                        "tx.taxonid like :filter",
                        "acc.accenumb like :filter",
                        "acc.accename like :filter",
                        "acc.puid like :filter",
                        "ctry.iso3 like :filter",
                        "bs.name like :filter",
                        "mls.name like :filter",
                        "inst.instcode like :filter",
                        "acc.maintainernumb like :filter"
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
                if ($order['name'] == 'taxonid') {
                    $orderColumn = 'tx.taxonid';
                }

                if ($order['name'] == 'accenumb') {
                    $orderColumn = 'acc.accenumb';
                }

                if ($order['name'] == 'accename') {
                    $orderColumn = 'acc.accename';
                }

                if ($order['name'] == 'puid') {
                    $orderColumn = 'acc.puid';
                }

                if ($order['name'] == 'country_iso3') {
                    $orderColumn = 'ctry.iso3';
                }

                if ($order['name'] == 'bs_name') {
                    $orderColumn = 'bs.name';
                }

                if ($order['name'] == 'mls_name') {
                    $orderColumn = 'mls.name';
                }

                if ($order['name'] == 'inst_instcode') {
                    $orderColumn = 'inst.instcode';
                }

                if ($order['name'] == 'maintainernumb') {
                    $orderColumn = 'acc.maintainernumb';
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

    // to retrieve the species
    // species are taxon used in an accession
    public function getSpecies() {
        $query = $this->createQueryBuilder('acc')
            ->select('DISTINCT tx.taxonid, tx.species, tx.genus, tx.subtaxa')
            ->join('App\Entity\Taxonomy', 'tx')
            ->where('acc.taxon = tx.id')
            ->orderBy('tx.species', 'ASC')
        ;
        return $query->getQuery()->getArrayResult();
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
