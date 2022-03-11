<?php
/* 
    This class is to be called in the FactorTypeController to update the FactorTypeForm.
    March 11, 2022
    David PIERRE
*/

namespace App\Form;

use App\Entity\FactorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactorUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ontology_id')
            ->add('name')
            ->add('parentTerm')
            ->add('description')
            ->add('createdAt')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FactorType::class,
        ]);
    }
}
