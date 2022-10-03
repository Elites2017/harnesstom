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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class AccessionUpdateType extends AbstractType
{
    private $router;

    function __construct(RouterInterface $router){
        $this->router = $router;
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
            ->add('accenumb')
            ->add('accename')
            ->add('puid')
            ->add('origmuni')
            ->add('origadmin1')
            ->add('origadmin2')
            ->add('maintainernumb')
            ->add('acqdate', DateType::class, array(
                'widget' => 'single_text'
            ))
            ->add('donornumb')
            ->add('collnumb')
            ->add('colldate', DateType::class, array(
                'widget' => 'single_text'
            ))
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
            ->add('instcode', EntityType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('storage', EntityType::class, [
                'class' => StorageType::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlStorageType .'" target="_blank">Storage Type</a>'
                
            ])
            ->add('donorcode', EntityType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('collcode', EntityType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlInstitute .'" target="_blank">Institute</a>'
                
            ])
            ->add('collmissid', EntityType::class, [
                'class' => CollectingMission::class,
                'help_html' => true,
                'placeholder' => '',
                'help' => 'Add a new <a href="' . $toUrlCollectingMissionIdentifier .'" target="_blank">Collecting Mission</a>'
                
            ])
            ->add('bredcode', EntityType::class, [
                'class' => Institute::class,
                'help_html' => true,
                'placeholder' => '',
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
