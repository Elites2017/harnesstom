<?php

namespace App\Form;

use App\Entity\ObservationVariable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationVariableUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('mainAbbreviaition')
            ->add('description')
            ->add('isActive')
            ->add('trait')
            ->add('scale')
            ->add('observationVariableMethod')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationVariable::class,
        ]);
    }
}