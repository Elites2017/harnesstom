<?php

namespace App\Form;

use App\Entity\Analyte;
use App\Entity\MetaboliteClass;
use App\Entity\AnalyteFlavorHealth;
use App\Entity\AnnotationLevel;
use App\Entity\IdentificationLevel;
use App\Entity\ObservationVariableMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AnalyteUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlAnnotationLevel = $this->router->generate('annotation_level_create');
        $toUrlIdentificationLevel = $this->router->generate('identification_level_create');
        $toUrlObservationVariableMethod = $this->router->generate('observation_variable_method_create');
        $toUrlMetaboliteClass = $this->router->generate('metabolite_class_create');
        $toUrlHealthFlavor = $this->router->generate('flavor_health_create');

        $builder
            ->add('name')
            ->add('analyteCode')
            ->add('retentionTime')
            ->add('massToChargeRatio')
            ->add('annotationLevel', DatalistType::class, [
                'class' => AnnotationLevel::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlAnnotationLevel .'" target="_blank">Annotation Level</a>'
                
            ])
            ->add('identificationLevel', Datalist1Type::class, [
                'class' => IdentificationLevel::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlIdentificationLevel .'" target="_blank">Identification Level</a>'
                
            ])
            ->add('observationVariableMethod', Datalist2Type::class, [
                'class' => ObservationVariableMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlObservationVariableMethod .'" target="_blank">ObservationVariable Method</a>'
                
            ])
            ->add('metaboliteClass', Datalist3Type::class, [
                'class' => MetaboliteClass::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlMetaboliteClass .'" target="_blank">Metabolite Class</a>'
                
            ])
            ->add('healthAndFlavor', Datalist4Type::class, [
                'class' => AnalyteFlavorHealth::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlHealthFlavor .'" target="_blank">Health & Flavor</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Analyte::class,
        ]);
    }
}
