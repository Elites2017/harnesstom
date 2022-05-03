<?php

namespace App\Form;

use App\Entity\ObservationLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationLevelUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('unitname')
            ->add('name')
            ->add('blockNumber')
            ->add('subBlockNumber')
            ->add('plotNumber')
            ->add('plantNumber')
            ->add('replicate')
            ->add('unitPosition')
            ->add('unitCoordinateX')
            ->add('unitCoordinateY')
            ->add('unitCoordinateXType')
            ->add('unitCoordinateYType')
            ->add('germaplasm')
            ->add('study')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationLevel::class,
        ]);
    }
}
