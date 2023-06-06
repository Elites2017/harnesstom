<?php

namespace App\Form;

use App\Entity\DataType;
use App\Entity\Scale;
use App\Entity\ScaleCategory;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ScaleType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlScaleCategory = $this->router->generate('scale_category_create');
        $toUrlDataType = $this->router->generate('data_type_create');
        $toUrlUnit = $this->router->generate('unit_create');

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('dataType', EntityType::class, [
                'class' => DataType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlDataType .'" target="_blank">Data Type</a>'
                
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
            'data_class' => Scale::class,
        ]);
    }
}
