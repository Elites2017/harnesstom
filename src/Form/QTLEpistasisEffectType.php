<?php

namespace App\Form;

use App\Entity\QTLEpistasisEffect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QTLEpistasisEffectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addEpi')
            ->add('r2Add')
            ->add('r2Epi')
            ->add('epistatisticEpi')
            ->add('qtlVariant1')
            ->add('qtlVariant2')
            ->add('epistatisticAdd')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QTLEpistasisEffect::class,
        ]);
    }
}
