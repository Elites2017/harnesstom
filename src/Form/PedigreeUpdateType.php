<?php

namespace App\Form;

use App\Entity\Cross;
use App\Entity\Pedigree;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class PedigreeUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tosUrlCross = $this->router->generate('cross_create');

        $builder
            ->add('pedigreeEntryID')
            ->add('generation')
            ->add('ancestorPedigreeEntryID')
            ->add('pedigreeCross', EntityType::class, [
                'class' => Cross::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $tosUrlCross .'" target="_blank">Cross</a>'
                
            ])
            ->add('germplasm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pedigree::class,
        ]);
    }
}
