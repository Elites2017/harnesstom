<?php

namespace App\Form;

use App\Entity\Scale;
use App\Entity\ScaleCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ScaleCategoryUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlScale = $this->router->generate('scale_create');

        $builder
            ->add('label')
            ->add('score')
            ->add('min')
            ->add('max')
            ->add('scale', DatalistType::class, [
                'class' => Scale::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlScale .'" target="_blank">Scale</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScaleCategory::class,
        ]);
    }
}
