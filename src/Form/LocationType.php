<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('longitudeCo')
            ->add('latitudeCo')
            ->add('altitudeCo')
            ->add('siteStatus')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'iso3'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
