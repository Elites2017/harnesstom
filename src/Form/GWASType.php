<?php

namespace App\Form;

use App\Entity\GWAS;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GWASType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('preprocessing')
            ->add('thresholdValue')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => 'Publication reference...'
            ])
            ->add('variantSetMetada')
            ->add('software')
            ->add('gwasModel')
            ->add('kinshipAlgorithm')
            ->add('structureMethod')
            ->add('geneticTestingModel')
            ->add('allelicEffectEstimator')
            ->add('gwasStatTest')
            ->add('thresholdMethod')
            ->add('studyList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GWAS::class,
        ]);
    }
}
