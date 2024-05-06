<?php

namespace App\Form;

use App\Entity\QTLEpistasisEffect;
use App\Entity\QTLVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class QTLEpistasisEffectType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlQtlVariant = $this->router->generate('qtl_variant_create');

        $builder
            ->add('addEpi')
            ->add('r2Add')
            ->add('r2Epi')
            ->add('epistatisticEpi')
            ->add('qtlVariant1', DatalistType::class, [
                'class' => QTLVariant::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlQtlVariant .'" target="_blank">QTL Variant</a>'
                
            ])
            ->add('qtlVariant2', Datalist1Type::class, [
                'class' => QTLVariant::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlQtlVariant .'" target="_blank">QTL Variant</a>'
                
            ])
            ->add('epistatisticAdd')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QTLEpistasisEffect::class,
        ]);
    }
}
