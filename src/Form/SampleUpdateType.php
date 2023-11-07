<?php

namespace App\Form;

use App\Entity\Germplasm;
use App\Entity\ObservationLevel;
use App\Entity\Sample;
use App\Entity\Study;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// call the trial public release service
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\RouterInterface;

class SampleUpdateType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlStudy = $this->router->generate('study_create');
        $toUrlObsLevel = $this->router->generate('observation_level_create');
        $toUrlGermplasm = $this->router->generate('germplasm_create');

        $builder
            ->add('name')
            ->add('replicate')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false])
            ->add('study', EntityType::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Study</a>'
            ])
            ->add('germplasm', EntityType::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
            ->add('developmentalStage')
            ->add('anatomicalEntity')
            ->add('observationLevel', EntityType::class, [
                'class' => ObservationLevel::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'query_builder' => $this->pubRelTrialService->getVisibleObservationLevels(),
                'help' => 'Add a new <a href="' . $toUrlObsLevel .'" target="_blank">Observation Level</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sample::class,
        ]);
    }
}
