<?php

namespace App\Form;

use App\Entity\Trial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('abbreviation')
            ->add('startDate')
            ->add('endDate')
            ->add('publicReleaseDate')
            ->add('license')
            ->add('pui')
            ->add('publicationReference')
            ->add('program')
            ->add('trialType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trial::class,
        ]);
    }
}
