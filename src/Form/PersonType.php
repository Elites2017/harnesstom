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

class PersonType extends AbstractType
{

    private $countryRepo;

    function __construct(CountryRepository $countryRepo){
        $this->countryRepo = $countryRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('middleName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('streetNumber')
            ->add('postalCode')
            ->add('city')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'required' => true,
                'placeholder' => 'Select a country',
                'query_builder' => function() {
                    return $this->countryRepo->createQueryBuilder('country')->orderBy('country.iso3', 'ASC');
                }
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
