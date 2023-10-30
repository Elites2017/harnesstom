<?php

namespace App\Form;

use App\Entity\Inquiry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName')
            ->add('subject')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Data Submission' => 'Data Submission',
                    'Technical Issues' => 'Technical Issues',
                    'Data Curation' => 'Data Curation',
                    'Privacy Policy' => 'Privacy Policy',
                    'Other' => 'Other'
                ]
            ])
            ->add('email')
            ->add('message', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('file', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inquiry::class,
        ]);
    }
}
