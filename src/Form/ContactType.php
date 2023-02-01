<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Institute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ContactType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlInstitute = $this->router->generate('institute_create');

        $builder
            ->add('orcid')
            ->add('person')
            ->add('institute', DatalistType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    '' => null,
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
