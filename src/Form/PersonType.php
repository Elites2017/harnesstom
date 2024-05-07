<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Person;
use App\Entity\User;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class PersonType extends AbstractType
{
    private $router;
    private $countryRepo;

    function __construct(RouterInterface $router, CountryRepository $countryRepo){
        $this->router = $router;
        $this->countryRepo = $countryRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCountry = $this->router->generate('country_create');

        $builder
            ->add('firstName')
            ->add('middleName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('streetNumber')
            ->add('postalCode')
            ->add('city')
            ->add('country', DatalistType::class, [
                'class' => Country::class,
                'help_html' => true,
                'placeholder' => 'Select a country',
                'query_builder' => function() {
                    return $this->countryRepo->createQueryBuilder('country')->orderBy('country.iso3', 'ASC');
                },
                'choice_value' => 'iso3',
                'help' => 'Add a new <a href="' . $toUrlCountry .'" target="_blank">Country</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
