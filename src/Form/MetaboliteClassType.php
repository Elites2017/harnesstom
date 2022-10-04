<?php

namespace App\Form;

use App\Entity\MetaboliteClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaboliteClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ontology_id')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7')])
            ->add('parentTerm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MetaboliteClass::class,
        ]);
    }
}
