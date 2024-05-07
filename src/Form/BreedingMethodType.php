<?php

namespace App\Form;

use App\Entity\BreedingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BreedingMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ontology_id')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('parentTerm', DatalistType::class, [
                'class' => BreedingMethod::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BreedingMethod::class,
        ]);
    }
}
