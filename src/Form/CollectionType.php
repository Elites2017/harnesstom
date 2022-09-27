<?php

namespace App\Form;

use App\Entity\CollectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('publicationReference')
            ->add('createdAt')
            ->add('isActive')
            ->add('createdBy')
            ->add('germplasm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectionClass::class,
        ]);
    }
}
