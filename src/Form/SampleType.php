<?php

namespace App\Form;

use App\Entity\AnatomicalEntity;
use App\Entity\DevelopmentalStage;
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

class SampleType extends AbstractType
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
        $toUrlAnatomicalEntity = $this->router->generate('anatomical_entity_create');
        $toUrlDevelopmentalStage = $this->router->generate('developmental_stage_create');

        $builder
            ->add('name')
            ->add('replicate')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false])
            ->add('study', DatalistType::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Study</a>'
            ])
            ->add('germplasm', Datalist1Type::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'germplasmID',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
            ->add('anatomicalEntity', Datalist2Type::class, [
                'class' => AnatomicalEntity::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlAnatomicalEntity .'" target="_blank">Anatomical Entity</a>'
            ])
            ->add('developmentalStage', Datalist3Type::class, [
                'class' => DevelopmentalStage::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlDevelopmentalStage .'" target="_blank">Experimental Design Type</a>'
                
            ])
            ->add('observationLevel', Datalist4Type::class, [
                'class' => ObservationLevel::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'unitname',
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
