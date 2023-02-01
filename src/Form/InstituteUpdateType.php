<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Institute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class InstituteUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCountry = $this->router->generate('country_create');
        $builder
            ->add('instcode')
            ->add('acronym')
            ->add('name')
            ->add('streetNumber')
            ->add('postalCode')
            ->add('city')
            ->add('country', DatalistType::class, [
                'class' => Country::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'choice_value' => 'iso3',
                'help' => 'Add a new <a href="' . $toUrlCountry .'" target="_blank">Country</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Institute::class,
        ]);
    }
}
