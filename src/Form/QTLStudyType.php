<?php

namespace App\Form;

use App\Entity\QTLStudy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QTLStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('qtlCount')
            ->add('thresholdValue')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => 'Publication reference...'
            ])
            ->add('ciCriteria')
            ->add('thresholdMethod')
            ->add('software')
            ->add('multiEnvironmentStat')
            ->add('method')
            ->add('variantSet')
            ->add('mappingPopulation')
            ->add('genomeMapUnit')
            ->add('statistic')
            ->add('studyList')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QTLStudy::class,
        ]);
    }
}
