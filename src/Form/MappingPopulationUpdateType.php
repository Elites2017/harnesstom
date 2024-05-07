<?php

namespace App\Form;

use App\Entity\MappingPopulation;
use App\Entity\Generation;
use App\Entity\Cross;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MappingPopulationUpdateType extends AbstractType
{
    private $router;

    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCross = $this->router->generate('cross_create');
        $toUrlPedigree = $this->router->generate('generation_create');

        $builder
            ->add('name')
            ->add('mappingPopulationCross', DatalistType::class, [
                'class' => Cross::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'query_builder' => $this->pubRelTrialService->getVisibleCrosses(),
                'help' => 'Add a new <a href="' . $toUrlCross .'" target="_blank">Cross</a>'
            ])
            ->add('pedigreeGeneration', Datalist1Type::class, [
                'class' => Generation::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
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
