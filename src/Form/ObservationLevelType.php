<?php

namespace App\Form;

use App\Entity\Germplasm;
use App\Entity\ObservationLevel;
use App\Entity\Study;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// call the trial public release service
use App\Service\PublicReleaseTrial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\RouterInterface;

class ObservationLevelType extends AbstractType
{
    private $router;
    private $pubRelTrialService;

    function __construct(RouterInterface $router, PublicReleaseTrial $pubRelTrialService){
        $this->router = $router;
        $this->pubRelTrialService = $pubRelTrialService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlStudy = $this->router->generate('study_create');
        $toUrlGermplasm = $this->router->generate('germplasm_create');

        $builder
            ->add('unitname')
            ->add('name')
            ->add('blockNumber')
            ->add('subBlockNumber')
            ->add('plotNumber')
            ->add('plantNumber')
            ->add('replicate')
            ->add('unitPosition')
            ->add('unitCoordinateX')
            ->add('unitCoordinateY')
            ->add('unitCoordinateXType')
            ->add('unitCoordinateYType')
            ->add('germaplasm', DatalistType::class, [
                'class' => Germplasm::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'germplasmID',
                'help' => 'Add a new <a href="' . $toUrlGermplasm .'" target="_blank">Germplasm</a>'
            ])
            ->add('study', Datalist1Type::class, [
                'class' => Study::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'query_builder' => $this->pubRelTrialService->getVisibleStudies(),
                'help' => 'Add a new <a href="' . $toUrlStudy .'" target="_blank">Study</a>'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObservationLevel::class,
        ]);
    }
}
