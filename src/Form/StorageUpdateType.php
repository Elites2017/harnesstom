<?php

namespace App\Form;

use App\Entity\StorageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StorageUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('code')
            ->add('parentTerm')
            ->add('createdAt')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StorageType::class,
        ]);
    }
}
