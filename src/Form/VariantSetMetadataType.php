<?php

namespace App\Form;

use App\Entity\GenotypingPlatform;
use App\Entity\VariantSetMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('filters')
            ->add('variantCount')
            ->add('publicationRef')
            ->add('dataUpload')
            ->add('fileUrl', FileType::class)
            ->add('genotypingPlatform')
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
