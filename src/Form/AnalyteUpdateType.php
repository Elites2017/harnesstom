<?php

namespace App\Form;

use App\Entity\Analyte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyteUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('analyteCode')
            ->add('retentionTime')
            ->add('massToChargeRatio')
            ->add('isActive')
            ->add('annotationLevel')
            ->add('identificationLevel')
            ->add('observationVariableMethod')
            ->add('analyteClass')
            ->add('healthAndFlavor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Analyte::class,
        ]);
    }
}
