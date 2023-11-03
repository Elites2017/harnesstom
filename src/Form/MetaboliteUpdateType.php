<?php

namespace App\Form;

use App\Entity\Analyte;
use App\Entity\MetabolicTrait;
use App\Entity\Metabolite;
use App\Entity\Scale;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MetaboliteUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlAnalyte = $this->router->generate('analyte_create');
        $toUrlMetabolicTrait = $this->router->generate('metabolic_trait_create');
        $toUrlScale = $this->router->generate('scale_create');

        $builder
            ->add('analyte', EntityType::class, [
                'class' => Analyte::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlAnalyte .'" target="_blank">Analyte</a>'
                
            ])
            ->add('metabolicTrait', EntityType::class, [
                'class' => MetabolicTrait::class,
                'help_html' => true,
                'required' => false,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlMetabolicTrait .'" target="_blank">Metabolic Trait</a>'
                
            ])
            ->add('scale', EntityType::class, [
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
            'data_class' => Metabolite::class,
        ]);
    }
}
