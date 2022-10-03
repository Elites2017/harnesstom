<?php

namespace App\Form;

use App\Entity\MappingPopulation;
use App\Entity\Pedigree;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MappingPopulationUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlMappingPopulation = $this->router->generate('mapping_population_create');
        $toUrlPedigree = $this->router->generate('pedigree_create');

        $builder
            ->add('name')
            ->add('mappingPopulationCross', EntityType::class, [
                'class' => MappingPopulation::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlMappingPopulation .'" target="_blank">Mapping Population</a>'
                
            ])
            ->add('pedigreeGeneration', EntityType::class, [
                'class' => Pedigree::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlPedigree .'" target="_blank">Pedigree</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MappingPopulation::class,
        ]);
    }
}
