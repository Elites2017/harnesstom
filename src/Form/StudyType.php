<?php

namespace App\Form;

use App\Entity\ExperimentalDesignType;
use App\Entity\FactorType;
use App\Entity\GrowthFacilityType;
use App\Entity\Institute;
use App\Entity\Location;
use App\Entity\Parameter;
use App\Entity\Season;
use App\Entity\Study;
use App\Entity\Trial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
// call the trial public release service
use App\Service\PublicReleaseTrial;

class StudyType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlSeason = $this->router->generate('season_create');
        $toUrlTrial = $this->router->generate('trial_create');
        $toUrlFactor = $this->router->generate('factor_type_create');
        $toUrlInstitute = $this->router->generate('institute_create');
        $toUrlLocation = $this->router->generate('location_create');
        $toUrlParameter = $this->router->generate('parameter_create');
        $toUrlExperimentalDesignType = $this->router->generate('experimental_design_create');
        $toUrlGrowthFaciliType = $this->router->generate('growth_facility_type_create');

        $builder
            ->add('name')
            ->add('abbreviation')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false])
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('culturalPractice')
            ->add('factor', DatalistType::class, [
                'class' => FactorType::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlFactor .'" target="_blank">Factor Type</a>'
                
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlSeason .'" target="_blank">Season</a>'
                
            ])
            ->add('institute', Datalist1Type::class, [
                'class' => Institute::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('location', Datalist2Type::class, [
                'class' => Location::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlLocation .'" target="_blank">Location</a>'
                
            ])
            ->add('growthFacility', Datalist3Type::class, [
                'class' => GrowthFacilityType::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlGrowthFaciliType .'" target="_blank">Growth Facility Type</a>'
                
            ])
            ->add('experimentalDesignType', Datalist4Type::class, [
                'class' => ExperimentalDesignType::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlExperimentalDesignType .'" target="_blank">Experimental Design Type</a>'
                
            ])
            ->add('trial', Datalist5Type::class, [
                'class' => Trial::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'abbreviation',
                'query_builder' => $this->pubRelTrialService->getPublicReleaseTrials(),
                'help' => 'Add a new <a href="' . $toUrlTrial .'" target="_blank">Trial</a>'
            ])
            ->add('extra', CollectionType::class, [
                'entry_type' => ParameterValueType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('growthFacilityDescription', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false])
            ->add('observationUnitsDescription', TextareaType::class, [
                    'attr' => array('cols' => '5', 'rows' => '7'),
                    'required' => false])
            ->add('experimentalDesignDescription', TextareaType::class, [
                        'attr' => array('cols' => '5', 'rows' => '7'),
                        'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Study::class,
        ]);
    }
}
