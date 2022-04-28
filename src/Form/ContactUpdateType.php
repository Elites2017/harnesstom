<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orcid')
            ->add('person')
            ->add('institute')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Program Leader' => 'Program Leader',
                    'Coordinator' => 'Coordinator',
                    'Adviser' => 'Adviser',
                    'Submitter' => 'Submitter'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
