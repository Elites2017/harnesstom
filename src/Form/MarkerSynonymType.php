<?php

namespace App\Form;

use App\Entity\Marker;
use App\Entity\MarkerSynonym;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class MarkerSynonymType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlMarker = $this->router->generate('marker_create');

        $builder
            ->add('synonymSource', TextType::class)
            ->add('markerSynonymId')
            ->add('markerName', EntityType::class, [
                'class' => Marker::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlMarker .'" target="_blank">Marker</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MarkerSynonym::class,
        ]);
    }
}
