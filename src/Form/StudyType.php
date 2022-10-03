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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class StudyType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
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
                'attr' => array('cols' => '5', 'rows' => '7')])
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
            ))
            ->add('culturalPractice')
            ->add('trial', EntityType::class, [
                'class' => Trial::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlTrial .'" target="_blank">Trial</a>'
                
            ])
            ->add('factor', EntityType::class, [
                'class' => FactorType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlFactor .'" target="_blank">Factor Type</a>'
                
            ])
            ->add('season', EntityType::class, [
                'class' => Season::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlSeason .'" target="_blank">Season</a>'
                
            ])
            ->add('institute', EntityType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlLocation .'" target="_blank">Location</a>'
                
            ])
            ->add('growthFacility', EntityType::class, [
                'class' => GrowthFacilityType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGrowthFaciliType .'" target="_blank">Growth Facility Type</a>'
                
            ])
            ->add('parameter', EntityType::class, [
                'class' => Parameter::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlParameter .'" target="_blank">Parameter</a>'
                
            ])
            ->add('experimentalDesignType', EntityType::class, [
                'class' => ExperimentalDesignType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlExperimentalDesignType .'" target="_blank">Experimental Design Type</a>'
                
            ])
            ->add('germplasms')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Study::class,
        ]);
    }
}
