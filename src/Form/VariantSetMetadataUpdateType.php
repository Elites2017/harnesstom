<?php

namespace App\Form;

use App\Entity\VariantSetMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VariantSetMetadataUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('filters')
            ->add('variantCount')
            ->add('publicationRef')
            ->add('dataUpload')
            ->add('fileUrl', FileType::class, [
                'data_class' => null
            ])
            ->add('isActive')
            ->add('genotypingPlatform')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariantSetMetadata::class,
        ]);
    }
}
