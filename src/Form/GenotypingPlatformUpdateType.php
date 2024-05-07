<?php

namespace App\Form;

use App\Entity\GenotypingPlatform;
use App\Entity\SequencingInstrument;
use App\Entity\SequencingType;
use App\Entity\VarCallSoftware;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class GenotypingPlatformUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlSequencingInstrument = $this->router->generate('sequencing_instrument_create');
        $toUrlSequencingType = $this->router->generate('sequencing_type_create');
        $toUrlVarCallSoftware = $this->router->generate('var_call_software_create');

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('methodDescription')
            ->add('refSetName')
            ->add('publishedDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'by_reference' => true,
            ))
            ->add('bioProjectID')
            ->add('markerCount')
            ->add('assemblyPUI')
            ->add('publicationRef', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('sequencingInstrument', DatalistType::class, [
                'class' => SequencingInstrument::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlSequencingType .'" target="_blank">Sequencing Instrument</a>'
            ])
            ->add('sequencingType', Datalist1Type::class, [
                'class' => SequencingType::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlSequencingInstrument .'" target="_blank">Sequencing Type</a>'
            ])
            ->add('varCallSoftware', Datalist2Type::class, [
                'class' => VarCallSoftware::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlVarCallSoftware .'" target="_blank">Var Call Software</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GenotypingPlatform::class,
        ]);
    }
}
