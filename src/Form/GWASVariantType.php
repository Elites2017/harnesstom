<?php

namespace App\Form;

use App\Entity\GWAS;
use App\Entity\GWASVariant;
use App\Entity\Marker;
use App\Entity\Metabolite;
use App\Entity\ObservationVariable;
use App\Entity\TraitProcessing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class GWASVariantType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlMetabolite = $this->router->generate('metabolite_create');
        $toUrlTraitPreprocessing = $this->router->generate('trait_processing_create');
        $toUrlObservationVariable = $this->router->generate('observation_variable_create');
        $toUrlMarker = $this->router->generate('marker_create');
        $toUrlGwas = $this->router->generate('gwas_create');

        $builder
            ->add('name')
            ->add('alternativeAllele')
            ->add('maf')
            ->add('sampleSize')
            ->add('snppValue')
            ->add('adjustedPValue')
            ->add('allelicEffect')
            ->add('allelicEffectStat')
            ->add('allelicEffectdf')
            ->add('allelicEffStdE')
            ->add('beta')
            ->add('betaStdE')
            ->add('oddsRatio')
            ->add('ciLower')
            ->add('ciUpper')
            ->add('rSquareOfMode')
            ->add('rSquareOfModeWithSNP')
            ->add('rSquareOfModeWithoutSNP')
            ->add('refAllele')
            ->add('marker', DatalistType::class, [
                'class' => Marker::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlMarker .'" target="_blank">Marker</a>'
                
            ])
            ->add('metabolite', EntityType::class, [
                'class' => Metabolite::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlMetabolite .'" target="_blank">Metabolite</a>'
                
            ])
            ->add('gwas', EntityType::class, [
                'class' => GWAS::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGwas .'" target="_blank">GWAS</a>'
                
            ])
            ->add('traitPreprocessing', EntityType::class, [
                'class' => TraitProcessing::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlTraitPreprocessing .'" target="_blank">Trait Preprocessing</a>'
                
            ])
            ->add('observationVariable', EntityType::class, [
                'class' => ObservationVariable::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlObservationVariable .'" target="_blank">Observation Variable</a>'
                
            ])
            ->add(
                'typeOfData', 
                ChoiceType::class, 
                [
                    'choices' => [
                        'Observation Variable Data ' => 'obsVarData',
                        'Metabolite Data ' => 'metaboliteData',
                    ],
                'expanded' => true,
                'mapped' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GWASVariant::class,
        ]);
    }
}
