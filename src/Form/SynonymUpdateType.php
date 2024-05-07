<?php

namespace App\Form;

use App\Entity\Accession;
use App\Entity\Synonym;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class SynonymUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlAccession = $this->router->generate('accession_create');

        $builder
            ->add('synonymSource')
            ->add('synonymId')
            ->add('accession', DatalistType::class, [
                'class' => Accession::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'maintainerNumb',
                'help' => 'Add a new <a href="' . $toUrlAccession .'" target="_blank">Accession</a>'  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Synonym::class,
        ]);
    }
}
