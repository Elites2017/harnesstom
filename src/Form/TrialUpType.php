<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\Trial;
use App\Entity\TrialType as EntityTrialType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('abbreviation')
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('publicReleaseDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('license')
            ->add('pui')
            ->add('publicationReference')
            ->add('program', EntityType::class, [
                'class' => Program::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlProgram .'" target="_blank">Program</a>'
                
            ])
            ->add('trialType', EntityType::class, [
                'class' => EntityTrialType::class,
                'help_html' => true,
                'placeholder' => '',
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
