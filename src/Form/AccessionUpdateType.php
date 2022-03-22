<?php

namespace App\Form;

use App\Entity\Accession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessionUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accenumb')
            ->add('accename')
            ->add('puid')
            ->add('origmuni')
            ->add('origadmin1')
            ->add('origadmin2')
            ->add('maintainernumb')
            ->add('acqdate')
            ->add('donornumb')
            ->add('collnumb')
            ->add('colldate')
            ->add('declatitude')
            ->add('declongitude')
            ->add('elevation')
            ->add('collsite')
            ->add('isActive')
            ->add('origcty')
            ->add('collsrc')
            ->add('sampstat')
            ->add('taxon')
            ->add('instcode')
            ->add('storage')
            ->add('donorcode')
            ->add('collcode')
            ->add('collmissid')
            ->add('bredcode')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Accession::class,
        ]);
    }
}
