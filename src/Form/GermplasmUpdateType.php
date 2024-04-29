<?php

namespace App\Form;

use App\Entity\Accession;
use App\Entity\Germplasm;
use App\Entity\Institute;
use App\Entity\Program;
use App\Repository\AccessionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class GermplasmUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlProgram = $this->router->generate('program_create');
        $toUrlInstitute = $this->router->generate('institute_create');
        $toUrlAccession = $this->router->generate('accession_create');

        $builder
            ->add('germplasmID')
            ->add('preprocessing')
            // ->add('maintainerNumb', EntityType::class, [
            //     'class' => Accession::class,
            //     'query_builder' => function(AccessionRepository $accRep) {
            //         return $accRep->createQueryBuilder('accession')
            //         ->where('accession.instcode = 1');
            //     },
            //     'choice_label' => 'maintainerNumb'
            // ])
            ->add('program', EntityType::class, [
                'class' => Program::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlProgram .'" target="_blank">Program</a>'
                
            ])
            ->add('maintainerInstituteCode', DatalistType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            // this is the maintainer numb
            ->add('accession', Datalist1Type::class, [
                'class' => Accession::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'maintainerNumb',
                'help' => 'Add a new <a href="' . $toUrlAccession .'" target="_blank">Accession</a>'
                
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                //dd($data);
            }

        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Germplasm::class,
        ]);
    }
}
