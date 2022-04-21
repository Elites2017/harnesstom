<?php

namespace App\Form;

use App\Entity\AttributeTraitValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeTraitValueUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('publicationReference')
            ->add('isActive')
            ->add('trait')
            ->add('metabolicTrait')
            ->add('attribute')
            ->add('accession')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeTraitValue::class,
        ]);
    }
}
