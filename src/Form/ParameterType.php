<?php

namespace App\Form;

use App\Entity\FactorType;
use App\Entity\Parameter;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ParameterType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlFactor = $this->router->generate('factor_type_create');
        $toUrlUnit = $this->router->generate('unit_create');

        $builder
            ->add('name')
            ->add('factorType')
            ->add('factorType', EntityType::class, [
                'class' => FactorType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlFactor .'" target="_blank">Factor Type</a>'
                
            ])
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlUnit .'" target="_blank">Unit</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
        ]);
    }
}
