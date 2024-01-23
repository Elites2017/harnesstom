<?php

namespace App\Form;

use App\Entity\Attribute;
use App\Entity\AttributeCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AttributeType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlAttributeCategory = $this->router->generate('attribute_category_create');

        $builder
            ->add('name')
            ->add('abbreviation')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5'),
                'required' => false])
            ->add('publicationReference', CollectionType::class, [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'prototype' => true,
                    'label' => false,
                    'prototype_data' => ''
                ])
            ->add('category', EntityType::class, [
                'class' => AttributeCategory::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAttributeCategory .'" target="_blank">Category</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class,
        ]);
    }
}
