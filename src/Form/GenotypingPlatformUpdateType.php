<?php

namespace App\Form;

use App\Entity\GenotypingPlatform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenotypingPlatformUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('methodDescription')
            ->add('refSetName')
            ->add('publishedDate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('bioProjectID')
            ->add('markerCount')
            ->add('assemblyPUI')
            ->add('publicationRef')
            ->add('sequencingType')
            ->add('sequencingInstrument')
            ->add('varCallSoftware')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GenotypingPlatform::class,
        ]);
    }
}
