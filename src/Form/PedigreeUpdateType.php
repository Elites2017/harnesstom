<?php

namespace App\Form;

use App\Entity\Pedigree;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PedigreeUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pedigreeEntryID')
            ->add('generation')
            ->add('ancestorPedigreeEntryID')
            ->add('pedigreeCross')
            ->add('germplasm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pedigree::class,
        ]);
    }
}
