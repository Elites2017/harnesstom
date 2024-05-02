<?php

namespace App\Form;

use App\Entity\BreedingMethod;
use App\Entity\Cross;
use App\Entity\Germplasm;
use App\Entity\Institute;
use App\Entity\Study;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
// call the trial public release service
use App\Service\PublicReleaseTrial;

class CrossUpdateType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlInstitute = $this->router->generate('institute_create');
        $toUrlStudy = $this->router->generate('study_create');
        $toUrlGermplasm = $this->router->generate('germplasm_create');
        $toUrlBreedingMethod = $this->router->generate('breeding_method_create');

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false ])
            ->add('parent1Type')
            ->add('parent2Type')
            ->add('year')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('study', DatalistType::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'abbreviation',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Study</a>'
            ])
            ->add('institute', Datalist1Type::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'required' => false,
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
            ])
            ->add('breedingMethod', Datalist4Type::class, [
                'class' => BreedingMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'required' => false,
                'help' => 'Add a new <a href="' . $toUrlBreedingMethod .'" target="_blank">Breeding Method</a>'
            ])
            ->add('parent1', Datalist2Type::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'germplasmID',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
            ->add('parent2', Datalist3Type::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'germplasmID',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cross::class,
        ]);
    }
}
