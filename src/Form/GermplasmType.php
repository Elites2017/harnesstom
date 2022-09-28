<?php

namespace App\Form;

use App\Entity\Accession;
use App\Entity\Germplasm;
use App\Repository\AccessionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GermplasmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ->add('program')
            ->add('maintainerInstituteCode')
            // this is the maintainer numb
            ->add('accession', EntityType::class, [
                'class' => Accession::class,
                'choice_label' => 'maintainerNumb'

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
