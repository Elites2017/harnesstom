<?php

namespace App\Form;

use App\Entity\MappingPopulation;
use App\Entity\Generation;
use App\Entity\Cross;
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
        $toUrlCross = $this->router->generate('cross_create');
        $toUrlPedigree = $this->router->generate('generation_create');

        $builder
            ->add('name')
            ->add('mappingPopulationCross', EntityType::class, [
                'class' => Cross::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlCross .'" target="_blank">Cross</a>'
                
            ])
            ->add('pedigreeGeneration', EntityType::class, [
                'class' => Generation::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlPedigree .'" target="_blank">Generation</a>'
                
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
