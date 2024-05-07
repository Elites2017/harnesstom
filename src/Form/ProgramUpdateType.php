<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Crop;
use App\Entity\Program;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ProgramUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCrop = $this->router->generate('crop_create');
        $toUrlContact = $this->router->generate('contact_create');
        $builder
            ->add('name', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '5')])
            ->add('abbreviation')
            ->add('objective', TextareaType::class, [
                'attr' => array('cols' => '5', 'rows' => '7'),
                'required' => false])
            ->add('externalRef')
            ->add('crop', DatalistType::class, [
                'class' => Crop::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'commonCropName',
                'help' => 'Add a new <a href="' . $toUrlCrop .'" target="_blank">Crop</a>'
                
            ])
            ->add('contact', Datalist1Type::class, [
                'class' => Contact::class,
                'help_html' => true,
                'required' => true,
                'placeholder' => '',
                'choice_value' => 'orcid',
                'help' => 'Add a new <a href="' . $toUrlContact .'" target="_blank">Contact</a>'
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
