<?php

namespace App\Form;

use App\Entity\ObservationVariable;
use App\Entity\ObservationVariableMethod;
use App\Entity\Scale;
use App\Entity\TraitClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ObservationVariableType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlTrait = $this->router->generate('trait_class_create');
        $toUrlScale = $this->router->generate('scale_create');
        $toUrlObservationVariableMethod = $this->router->generate('observation_variable_method_create');

        $builder
            ->add('name')
            ->add('mainAbbreviaition')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('trait', DatalistType::class, [
                'class' => TraitClass::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlTrait .'" target="_blank">Trait</a>'
            ])
            ->add('scale', Datalist1Type::class, [
                'class' => Scale::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlScale .'" target="_blank">Scale</a>'
            ])
            ->add('observationVariableMethod', Datalist2Type::class, [
                'class' => ObservationVariableMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlObservationVariableMethod .'" target="_blank">Observation Variable Method</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationVariable::class,
        ]);
    }
}
