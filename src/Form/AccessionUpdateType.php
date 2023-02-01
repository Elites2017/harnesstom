<?php

namespace App\Form;

use App\Entity\Accession;
use App\Entity\BiologicalStatus;
use App\Entity\CollectingMission;
use App\Entity\CollectingSource;
use App\Entity\Country;
use App\Entity\Institute;
use App\Entity\MLSStatus;
use App\Entity\StorageType;
use App\Entity\Taxonomy;
use App\Repository\AccessionRepository;
use App\Repository\InstituteRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AccessionUpdateType extends AbstractType
{
    private $router;
    private $instituteRepo;
    private $acceRepo;

    function __construct(RouterInterface $router, InstituteRepository $instituteRepo, AccessionRepository $acceRepo){
        $this->router = $router;
        $this->instituteRepo = $instituteRepo;
        $this->acceRepo = $acceRepo;
    }

    private function myChoices($institute) {
        $results = $this->acceRepo->findBy(['instcode' => $institute], ['maintainernumb' => 'ASC']);
        $businessUnit = array();
        foreach($results as $bu){
            $businessUnit[$bu->getMaintainernumb()] = $bu->getMaintainernumb();
        }
        return $businessUnit;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $toUrlCountry = $this->router->generate('country_create');
        $toUrlCollectingSource = $this->router->generate('collecting_source_create');
        $toUrlBiologicalStatus = $this->router->generate('biological_status_create');
        $toUrlMLSStatus = $this->router->generate('mls_status_create');
        $toUrlTaxon = $this->router->generate('taxonomy_create');
        $toUrlStorageType = $this->router->generate('storage_type_create');
        $toUrlInstitute = $this->router->generate('institute_create');
        $toUrlCollectingMissionIdentifier = $this->router->generate('collecting_mission_create');

        $builder
            ->add('instcode', EntityType::class, [
            'class' => Institute::class,
            'help_html' => true,
            'placeholder' => '',
            'choice_value' => 'name',
            'query_builder' => function() {
                return $this->instituteRepo->createQueryBuilder('ins')->orderBy('ins.name', 'ASC');
            },
            'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
        ])
        ;
            $formModifier = function (FormInterface $form, Institute $institute = null) {
                $maintainerNumbers = null === $institute ? [] : $this->myChoices($institute);
                $form->add('maintainernumb', ChoiceType::class, [
                    'choices' => $maintainerNumbers ?? null,
                    'placeholder' => 'Please choose an accession number',
                    'disabled' => $maintainerNumbers === []
                ]);
            };

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event) use ($formModifier) {
                $data  = $event->getData();
                $formModifier($event->getForm(), $data->getInstcode());
            });

            $builder->get('instcode')->addEventListener(
                FormEvents::POST_SUBMIT,
                function(FormEvent $event) use ($formModifier) {
                    $institute = $event->getForm()->getData();
                    $formModifier($event->getForm()->getParent(), $institute);
                }
            );
        $builder
            ->add('accenumb')
            ->add('accename')
            ->add('puid')
            ->add('origmuni')
            ->add('origadmin1')
            ->add('origadmin2')
            ->add('acqdate')
            ->add('donornumb')
            ->add('collnumb')
            ->add('colldate')
            ->add('declatitude')
            ->add('declongitude')
            ->add('elevation')
            ->add('collsite')
            ->add('origcty', EntityType::class, [
                'class' => Country::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlCountry .'" target="_blank">Country</a>'
                
            ])
            ->add('collsrc', EntityType::class, [
                'class' => CollectingSource::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlCollectingSource .'" target="_blank">Collecting Source</a>'
                
            ])
            ->add('sampstat', EntityType::class, [
                'class' => BiologicalStatus::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlBiologicalStatus .'" target="_blank">Biological Status</a>'
                
            ])
            ->add('taxon', EntityType::class, [
                'class' => Taxonomy::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlTaxon .'" target="_blank">Taxonomy</a>'
                
            ])
            ->add('storage', EntityType::class, [
                'class' => StorageType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlStorageType .'" target="_blank">Storage Type</a>'
                
            ])
            ->add('donorcode', Datalist1Type::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('collcode', DatalistType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('collmissid', EntityType::class, [
                'class' => CollectingMission::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlCollectingMissionIdentifier .'" target="_blank">Collecting Mission</a>'
                
            ])
            ->add('bredcode', Datalist2Type::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'choice_value' => 'name',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('mlsStatus', EntityType::class, [
                'class' => MLSStatus::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlMLSStatus .'" target="_blank">MLS Status</a>'
                
            ])
            ->add('breedingInfo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Accession::class,
        ]);
    }
}
