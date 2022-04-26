<?php

namespace App\Form;

use App\Entity\Synonym;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SynonymUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tgrc')
            ->add('usda')
            ->add('comav')
            ->add('fma01')
            ->add('uib')
            ->add('pgr')
            ->add('eusol')
            ->add('cccode')
            ->add('ndl')
            ->add('avrc')
            ->add('inra')
            ->add('unitus')
            ->add('resqProject360')
            ->add('reseq150')
            ->add('accession')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Synonym::class,
        ]);
    }
}
