<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\Trial;
use App\Entity\TrialType as EntityTrialType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class TrialUpType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlProgram = $this->router->generate('program_create');
        $toUrlTrialType = $this->router->generate('trial_type_create');
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5'),
                'required' => false ])
            ->add('abbreviation')
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'by_reference' => true
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'required' => false,
                'by_reference' => true,
            ))
            ->add('publicReleaseDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('license')
            ->add('pui')
            ->add('publicationReference', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false,
                'prototype_data' => ''
            ])
            ->add('program', DatalistType::class, [
                'class' => Program::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'abbreviation',
                'help' => 'Add a new <a href="' . $toUrlProgram .'" target="_blank">Program</a>'
                
            ])
            ->add('trialType', Datalist1Type::class, [
                'class' => EntityTrialType::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlTrialType .'" target="_blank">Trial Type</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trial::class,
        ]);
    }
}
