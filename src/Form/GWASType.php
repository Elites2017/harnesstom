<?php

namespace App\Form;

use App\Entity\AllelicEffectEstimator;
use App\Entity\GeneticTestingModel;
use App\Entity\GWAS;
use App\Entity\GWASModel;
use App\Entity\GWASStatTest;
use App\Entity\KinshipAlgorithm;
use App\Entity\Software;
use App\Entity\StructureMethod;
use App\Entity\ThresholdMethod;
use App\Entity\VariantSetMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class GWASType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCicriteria = $this->router->generate('ci_criteria_create');
        $toUrlThresholdMethod = $this->router->generate('threshold_method_create');
        $toUrlSoftware = $this->router->generate('software_create');
        $toUrlVariantSetMetadata = $this->router->generate('variant_set_metadata_create');
        $toUrlGWASModel = $this->router->generate('gwas_model_create');
        $toUrlKinshipAlgo = $this->router->generate('kinship_algorithm_create');
        $toUrlStructureMethod = $this->router->generate('structure_method_create');
        $toUrlGeneticTestingModel = $this->router->generate('genetic_testing_model_create');
        $toUrlAllelicEffectEstimator = $this->router->generate('allelic_effect_estimator_create');
        $toUrlGWASStatTest = $this->router->generate('gwas_stat_test_create');

        $builder
            ->add('name')
            ->add('preprocessing')
            ->add('thresholdValue')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('variantSetMetadata', EntityType::class, [
                'class' => VariantSetMetadata::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlVariantSetMetadata .'" target="_blank">Variant Set Metadata</a>'
                
            ])
            ->add('gwasModel', EntityType::class, [
                'class' => GWASModel::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGWASModel .'" target="_blank">GWAS Model</a>'
                
            ])
            ->add('kinshipAlgorithm', EntityType::class, [
                'class' => KinshipAlgorithm::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlKinshipAlgo .'" target="_blank">Kinship Algorithm</a>'
                
            ])
            ->add('structureMethod', EntityType::class, [
                'class' => StructureMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlStructureMethod .'" target="_blank">Structure Method</a>'
                
            ])
            ->add('geneticTestingModel', EntityType::class, [
                'class' => GeneticTestingModel::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGeneticTestingModel .'" target="_blank">Genetic Testing Model</a>'
                
            ])
            ->add('allelicEffectEstimator', EntityType::class, [
                'class' => AllelicEffectEstimator::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAllelicEffectEstimator .'" target="_blank">Allelic Effect Estimator</a>'
                
            ])
            ->add('gwasStatTest', EntityType::class, [
                'class' => GWASStatTest::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGWASStatTest .'" target="_blank">GWAS Stat Test</a>'
                
            ])
            ->add('thresholdMethod', EntityType::class, [
                'class' => ThresholdMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlThresholdMethod .'" target="_blank">Threshold Method</a>'
                
            ])
            ->add('software', EntityType::class, [
                'class' => Software::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlSoftware .'" target="_blank">Software</a>'
                
            ])
            ->add('studyList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GWAS::class,
        ]);
    }
}
