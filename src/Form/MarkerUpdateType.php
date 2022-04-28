<?php

namespace App\Form;

use App\Entity\Marker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkerUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('linkageGroupName')
            ->add('position')
            ->add('start')
            ->add('end')
            ->add('refAllele')
            ->add('altAllele')
            ->add('primerName1')
            ->add('primerSeq1')
            ->add('primerName2')
            ->add('primerSeq2')
            ->add('genotypingPlatform')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marker::class,
        ]);
    }
}
