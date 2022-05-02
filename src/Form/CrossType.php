<?php

namespace App\Form;

use App\Entity\Cross;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrossType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7')])
            ->add('parent1Type')
            ->add('parent2Type')
            ->add('year')
            ->add('publicationReference')
            ->add('study')
            ->add('institute')
            ->add('breedingMethod')
            ->add('parent1')
            ->add('parent2')
            ->add('pedigree')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cross::class,
        ]);
    }
}
