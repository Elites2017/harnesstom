<?php

namespace App\Form;

use App\Entity\CiCriteria;
use App\Entity\MappingPopulation;
use App\Entity\QTLMethod;
use App\Entity\QTLStatistic;
use App\Entity\QTLStudy;
use App\Entity\Software;
use App\Entity\Study;
use App\Entity\ThresholdMethod;
use App\Entity\Unit;
use App\Entity\VariantSetMetadata;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class QTLStudyUpdateType extends AbstractType
{
    private $router;

    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlThresholdMethod = $this->router->generate('threshold_method_create');
        $toUrlCicriteria = $this->router->generate('ci_criteria_create');
        $toUrlSoftware = $this->router->generate('software_create');
        $toUrlMultiEnvStat = $this->router->generate('qtl_statistic_create');
        $toUrlMethod = $this->router->generate('qtl_method_create');
        $toUrlVariantSetMetadata = $this->router->generate('variant_set_metadata_create');
        $toUrlMappingPopulation = $this->router->generate('mapping_population_create');
        $toUrlGenomeMapUnit = $this->router->generate('unit_create');
        $toUrlStatistic = $this->router->generate('qtl_statistic_create');
        $toUrlStudy = $this->router->generate('study_create');

        $builder
            ->add('name')
            ->add('qtlCount')
            ->add('thresholdValue')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('ciCriteria', EntityType::class, [
                'class' => CiCriteria::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlCicriteria .'" target="_blank">Ci Crriteria</a>'
                
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
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlSoftware .'" target="_blank">Software</a>'
                
            ])
            ->add('multiEnvironmentStat', EntityType::class, [
                'class' => QTLStatistic::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlMultiEnvStat .'" target="_blank">Multi Environment Stat</a>'
                
            ])
            ->add('method', EntityType::class, [
                'class' => QTLMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlMethod .'" target="_blank">QTL Method</a>'
                
            ])
            ->add('variantSetMetadata', EntityType::class, [
                'class' => VariantSetMetadata::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlVariantSetMetadata .'" target="_blank">Variant Set Metadata</a>'
                
            ])
            ->add('mappingPopulation', EntityType::class, [
                'class' => MappingPopulation::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getVisibleMappingPopulations(),
                'help' => 'Add a new <a href="' . $toUrlMappingPopulation .'" target="_blank">Mapping Population</a>'
                
            ])
            ->add('genomeMapUnit', EntityType::class, [
                'class' => Unit::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGenomeMapUnit .'" target="_blank">Genome Map Unit</a>'
                
            ])
            ->add('statistic', EntityType::class, [
                'class' => QTLStatistic::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlStatistic .'" target="_blank">QTL Statistic</a>'
                
            ])
            ->add('studyList', EntityType::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Study</a>',
                'multiple' => true
            ])
            ->add('epistasisStatistic', EntityType::class, [
                'class' => QTLStatistic::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlStatistic .'" target="_blank">QTL Statistic</a>'
                
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QTLStudy::class,
        ]);
    }
}