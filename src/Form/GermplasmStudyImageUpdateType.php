<?php

namespace App\Form;

use App\Entity\AnatomicalEntity;
use App\Entity\DevelopmentalStage;
use App\Entity\FactorType;
use App\Entity\Germplasm;
use App\Entity\GermplasmStudyImage;
use App\Entity\Study;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
// call the trial public release service
use App\Service\PublicReleaseTrial;

class GermplasmStudyImageUpdateType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlFactorType = $this->router->generate('factor_type_create');
        $toUrlStudy = $this->router->generate('study_create');
        $toUrlDevelopmentalStage = $this->router->generate('developmental_stage_create');
        $toUrlAnatomicalEntity = $this->router->generate('anatomical_entity_create');
        $toUrlGermplasm = $this->router->generate('germplasm_create');

        $builder
            ->add('filename', FileType::class, [
                'data_class' => null
            ])
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5'),
                'required' => false])
            ->add('factor', DatalistType::class, [
                'class' => FactorType::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlFactorType .'" target="_blank">Factor</a>'
            ])
            ->add('developmentStage', Datalist1Type::class, [
                'class' => DevelopmentalStage::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlDevelopmentalStage .'" target="_blank">Devlopmental Stage</a>'
            ])
            ->add('plantAnatomicalEntity', Datalist2Type::class, [
                'class' => AnatomicalEntity::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlAnatomicalEntity .'" target="_blank">Anatomical Entity</a>'
            ])
            ->add('GermplasmID', Datalist3Type::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'GermplasmID',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
            ->add('StudyID', Datalist4Type::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'abbreviation',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Trial</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GermplasmStudyImage::class,
        ]);
    }
}
