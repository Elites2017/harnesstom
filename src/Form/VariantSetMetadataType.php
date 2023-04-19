<?php

namespace App\Form;

use App\Entity\GenotypingPlatform;
use App\Entity\Software;
use App\Entity\VariantSetMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class VariantSetMetadataType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlGenotypingPlatform = $this->router->generate('genotyping_platform_create');
        $toUrlSoftware = $this->router->generate('software_create');

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('filters', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5'),
                'required' => false])
            ->add('variantCount', TextType::class, [
                'required' => false,
            ])
            ->add('publicationRef', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'label' => false
            ])
            ->add('dataUpload', FileType::class,[
                'data_class' => null
            ])
            ->add('software', EntityType::class, [
                'class' => Software::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlSoftware .'" target="_blank">Software</a>'
            ])
            ->add('genotypingPlatform', EntityType::class, [
                'class' => GenotypingPlatform::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGenotypingPlatform .'" target="_blank">Genotyping Platform</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariantSetMetadata::class,
        ]);
    }
}
