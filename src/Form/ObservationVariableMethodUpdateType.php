<?php

namespace App\Form;

use App\Entity\ObservationVariableMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationVariableMethodUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('instrument')
            ->add('software')
            ->add('publicationReference')
            ->add('isActive')
            ->add('methodClass')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationVariableMethod::class,
        ]);
    }
}
