<?php

namespace App\Form;

use App\Entity\GWASVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GWASVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('alternativeAllele')
            ->add('maf')
            ->add('sampleSize')
            ->add('snppValue')
            ->add('adjustedPValue')
            ->add('allelicEffect')
            ->add('allelicEffectStat')
            ->add('allelicEffectdf')
            ->add('allelicEffStdE')
            ->add('beta')
            ->add('betaStdE')
            ->add('oddsRatio')
            ->add('ciLower')
            ->add('ciUpper')
            ->add('rSquareOfMode')
            ->add('rSquareOfModeWithSNP')
            ->add('rSquareOfModeWithoutSNP')
            ->add('refAllele')
            ->add('marker')
            ->add('metabolite')
            ->add('gwas')
            ->add('traitPreprocessing')
            ->add('observationVariable')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GWASVariant::class,
        ]);
    }
}
