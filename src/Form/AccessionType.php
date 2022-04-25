<?php

namespace App\Form;

use App\Entity\Accession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessionType extends AbstractType
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
            ->add('acqdate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('donornumb')
            ->add('collnumb')
            ->add('colldate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('declatitude')
            ->add('declongitude')
            ->add('elevation')
            ->add('collsite')
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
            ->add('mlsStatus')
            ->add('breedingInfo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Accession::class,
        ]);
    }
}
