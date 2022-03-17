<?php

namespace App\Form;

use App\Entity\MetabolicTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetabolicTraitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ontology_id')
            ->add('description')
            ->add('chebiMonoIsoTopicMass')
            ->add('chebiMass')
            ->add('parentTerm')
            ->add('synonym')
            ->add('chebiLink')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MetabolicTrait::class,
        ]);
    }
}
