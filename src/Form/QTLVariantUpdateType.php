<?php

namespace App\Form;

use App\Entity\Germplasm;
use App\Entity\Marker;
use App\Entity\Metabolite;
use App\Entity\ObservationVariable;
use App\Entity\QTLStudy;
use App\Entity\QTLVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class QTLVariantUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $urlGermplasm = $this->router->generate('germplasm_create');
        $urlQTLStudy = $this->router->generate('qtl_study_create');
        $urlMarker = $this->router->generate('marker_create');
        $urlMetabolite = $this->router->generate('metabolite_create');
        $urlObservationVariable = $this->router->generate('observation_variable_create');

        $builder
            ->add('name')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('locusName')
            ->add('locus')
            ->add('r2QTLxE')
            ->add('r2Global')
            ->add('statisticQTLxEValue')
            ->add('r2')
            ->add('dA')
            ->add('dominance')
            ->add('additive')
            ->add('qtlStatsValue')
            ->add('positiveAllele')
            ->add('ciStart')
            ->add('ciEnd')
            ->add('detectName')
            ->add('originalTraitName')
            ->add('peakPosition')
            ->add('linkageGroupName')
            ->add('qtlStudy', DatalistType::class, [
                'class' => QTLStudy::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $urlQTLStudy .'" target="_blank">QTL Study</a>'
                
            ])
            ->add('observationVariable', Datalist1Type::class, [
                'class' => ObservationVariable::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $urlObservationVariable .'" target="_blank">Observation Variable</a>',
                'required' => false
                
            ])
            ->add('metabolite', Datalist2Type::class, [
                'class' => Metabolite::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'analyte',
                'help' => 'Add a new <a href="' . $urlMetabolite .'" target="_blank">Metabolite</a>',
                'required' => false
                
            ])
            ->add('positiveAlleleParent', Datalist6Type::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'germplasmID',
                'help' => 'Add a new <a href="' . $urlGermplasm .'" target="_blank">Germplasm</a>'
                
            ])
            ->add('closestMarker', Datalist3Type::class, [
                'class' => Marker::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $urlMarker .'" target="_blank">Marker</a>'
                
            ])
            ->add('flankingMarkerStart', Datalist4Type::class, [
                'class' => Marker::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $urlMarker .'" target="_blank">Marker</a>'
                
            ])
            ->add('flankingMarkerEnd', Datalist5Type::class, [
                'class' => Marker::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $urlMarker .'" target="_blank">Marker</a>'
                
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
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QTLVariant::class,
        ]);
    }
}
