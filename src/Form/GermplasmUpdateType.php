<?php

namespace App\Form;

use App\Entity\Germplasm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GermplasmUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('germplasmID')
            ->add('preprocessing')
            ->add('isActive')
            ->add('instcode')
            ->add('maintainerNumb')
            ->add('program')
            ->add('accession')
            ->add('study')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Germplasm::class,
        ]);
    }
}
