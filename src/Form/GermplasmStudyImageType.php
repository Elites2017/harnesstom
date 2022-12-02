<?php

namespace App\Form;

use App\Entity\AnatomicalEntity;
use App\Entity\DevelopmentalStage;
use App\Entity\FactorType;
use App\Entity\GermplasmStudyImage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class GermplasmStudyImageType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlFactorType = $this->router->generate('factor_type_create');
        $toUrlDevelopmentalStage = $this->router->generate('developmental_stage_create');
        $toUrlAnatomicalEntity = $this->router->generate('anatomical_entity_create');

        $builder
            ->add('filename', FileType::class)
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('factor', EntityType::class, [
                'class' => FactorType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlFactorType .'" target="_blank">Factor</a>'
            ])
            ->add('developmentStage', EntityType::class, [
                'class' => DevelopmentalStage::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlDevelopmentalStage .'" target="_blank">Devlopmental Stage</a>'
            ])
            ->add('plantAnatomicalEntity', EntityType::class, [
                'class' => AnatomicalEntity::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAnatomicalEntity .'" target="_blank">Anatomical Entity</a>'
            ])
            ->add('GermplasmID')
            ->add('StudyID')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GermplasmStudyImage::class,
        ]);
    }
}
