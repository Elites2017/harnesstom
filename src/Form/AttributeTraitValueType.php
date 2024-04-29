<?php

namespace App\Form;

use App\Entity\Accession;
use App\Entity\Attribute;
use App\Entity\AttributeTraitValue;
use App\Entity\MetabolicTrait;
use App\Entity\TraitClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AttributeTraitValueType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlTrait = $this->router->generate('trait_class_create');
        $toUrlMetabolicTrait = $this->router->generate('metabolic_trait_create');
        $toUrlAttribute = $this->router->generate('attribute_create');
        $toUrlAccession = $this->router->generate('accession_create');

        $builder
            ->add('value')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('trait', EntityType::class, [
                'class' => TraitClass::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlTrait .'" target="_blank">Trait</a>'
                
            ])
            ->add('metabolicTrait', EntityType::class, [
                'class' => MetabolicTrait::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlMetabolicTrait .'" target="_blank">Metabolic Trait</a>'
                
            ])
            ->add('attribute', EntityType::class, [
                'class' => Attribute::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAttribute .'" target="_blank">Attribute</a>'
                
            ])
            ->add('accession', EntityType::class, [
                'class' => Accession::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAccession .'" target="_blank">Accession</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeTraitValue::class,
        ]);
    }
}
