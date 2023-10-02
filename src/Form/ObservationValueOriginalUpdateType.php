<?php

namespace App\Form;

use App\Entity\ObservationLevel;
use App\Entity\ObservationValueOriginal;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ObservationValueOriginalUpdateType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlObsLevel = $this->router->generate('observation_level_create');
        $builder
            ->add('value')
            ->add('unitName', EntityType::class, [
                'class' => ObservationLevel::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getVisibleObservationLevels(),
                'help' => 'Add a new <a href="' . $toUrlObsLevel .'" target="_blank">Observation Level</a>'
            ])
            ->add('observationVariableOriginal')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationValueOriginal::class,
        ]);
    }
}
