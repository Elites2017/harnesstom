<?php

namespace App\Form;

use App\Entity\Marker;
use App\Entity\Sample;
use App\Entity\VariantSet;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class VariantSetType extends AbstractType
{
    private $router;

    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlSample = $this->router->generate('sample_create');
        $toUrlMarker = $this->router->generate('marker_create');
        
        $builder
            ->add('value')
            ->add('sample', EntityType::class, [
                'class' => Sample::class,
                'help_html' => true,
                'placeholder' => '',
                'query_builder' => $this->pubRelTrialService->getVisibleSamples(),
                'help' => 'Add a new <a href="' . $toUrlSample .'" target="_blank">Sample</a>'
                
            ])
            ->add('marker', DatalistType::class, [
                'class' => Marker::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlMarker .'" target="_blank">Institute</a>'
                
            ])
            ->add('variantSetMetadata')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariantSet::class,
        ]);
    }
}
