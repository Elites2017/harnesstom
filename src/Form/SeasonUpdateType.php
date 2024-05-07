<?php
/* 
    This class is to be called in the SeasonController to update the seasonForm.
    March 11, 2022
    David PIERRE
*/

namespace App\Form;

use App\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ontology_id')
            ->add('description', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7')])
            ->add('parentTerm', DatalistType::class, [
                'class' => Season::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
