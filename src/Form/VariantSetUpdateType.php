<?php

namespace App\Form;

use App\Entity\Marker;
use App\Entity\Sample;
use App\Entity\VariantSet;
use App\Entity\VariantSetMetadata;
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class VariantSetUpdateType extends AbstractType
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
        $toUrlVariantSetMetadata = $this->router->generate('variant_set_metadata_create');
        
        $builder
            ->add('value')
            ->add('sample', DatalistType::class, [
                'class' => Sample::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'query_builder' => $this->pubRelTrialService->getVisibleSamples(),
                'help' => 'Add a new <a href="' . $toUrlSample .'" target="_blank">Sample</a>'
            ])
            ->add('marker', Datalist1Type::class, [
                'class' => Marker::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlMarker .'" target="_blank">Marker</a>' 
            ])
            ->add('variantSetMetadata', Datalist2Type::class, [
                'class' => VariantSetMetadata::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlVariantSetMetadata .'" target="_blank">Variant Set Metadata</a>' 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariantSet::class,
        ]);
    }
}
