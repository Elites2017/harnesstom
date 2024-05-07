<?php

namespace App\Form;

use App\Entity\Cross;
use App\Entity\Generation;
use App\Entity\Pedigree;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class PedigreeType extends AbstractType
{
    private $router;

    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tosUrlCross = $this->router->generate('cross_create');
        $tosUrlGeneration = $this->router->generate('generation_create');

        $builder
            ->add('pedigreeEntryID')
            ->add('generation', DatalistType::class, [
                'class' => Generation::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $tosUrlGeneration .'" target="_blank">Generation</a>'
            ])
            ->add('pedigreeAncestorEntryId', Datalist1Type::class, [
                'class' => Pedigree::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'pedigreeEntryID',
            ])
            ->add('pedigreeCross', Datalist2Type::class, [
                'class' => Cross::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'query_builder' => $this->pubRelTrialService->getVisibleCrosses(),
                'help' => 'Add a new <a href="' . $tosUrlCross .'" target="_blank">Cross</a>' 
            ])
            ->add('germplasm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pedigree::class,
        ]);
    }
}
