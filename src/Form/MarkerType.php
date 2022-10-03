<?php

namespace App\Form;

use App\Entity\GenotypingPlatform;
use App\Entity\Marker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MarkerType extends AbstractType
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
            ->add('type')
            ->add('linkageGroupName')
            ->add('position')
            ->add('start')
            ->add('end')
            ->add('refAllele')
            ->add('altAllele')
            ->add('primerName1')
            ->add('primerSeq1')
            ->add('primerName2')
            ->add('primerSeq2')
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
            'data_class' => Marker::class,
        ]);
    }
}
