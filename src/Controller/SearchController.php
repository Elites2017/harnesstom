<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AccessionRepository;
use App\Repository\CountryRepository;
use App\Repository\BiologicalStatusRepository;
use App\Repository\CollectingMissionRepository;
use App\Repository\CollectingSourceRepository;
use App\Repository\InstituteRepository;
use App\Repository\MLSStatusRepository;
use App\Repository\TaxonomyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/search", name="search_")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/accession", name="accession")
     */
    public function index(AccessionRepository $accessionRepo, CountryRepository $countryRepo, BiologicalStatusRepository $biologicalStatusRepo,
                        MLSStatusRepository $mlsStatusRepo, TaxonomyRepository $taxonomyRepo, CollectingMissionRepository $collectingMissionRepo, 
                        CollectingSourceRepository $collectingSourceRepo, InstituteRepository $instituteRepo, Request $request): Response
    {
        // get the filters selected by the user
        $selectedCountries = $request->get("countries");
        $selectedBiologicalStatuses = $request->get("biologicalStatuses");
        $selectedMLSStatuses = $request->get('mlsStatuses');
        $selectedTaxonomies = $request->get('taxonomies');
        $selectedCollectingMissions = $request->get('collectingMissions');
        $selectedCollectingSources = $request->get('collectingSources');
        // institutes
        $selectedMaintainingInstitutes = $request->get('maintainingInstitutes');
        $selectedDonorInstitutes = $request->get('donorInstitutes');
        $selectedBreedingInstitutes = $request->get('breedingInstitutes');
        

        // filters
        $accessionsByCountry = $countryRepo->getAccessionCountries();
        $accessionsByBiologicalStatus = $biologicalStatusRepo->getAccessionsByBiologicalStatus();
        $accessionsByMLSStatus = $mlsStatusRepo->getAccessionsByMLSStatus();
        $accessionsByTaxonomy = $taxonomyRepo->getAccessionsByTaxonomy();
        //$accessionsBySpecies = $taxonomyRepo->getAccessionsBySpecies();
        $accessionsByCollectingSource = $collectingSourceRepo->getAccessionsByCollectingSource();
        $accessionsByCollectingMission = $collectingMissionRepo->getAccessionsByCollectingMission();
        // institutes
        $accessionsByMaintainingInstitute = $instituteRepo->getAccessionsByMaintainingInstitute();
        $accessionsByDonorInstitute = $instituteRepo->getAccessionsByDonorInstitute();
        $accessionsByBreedingInstitute = $instituteRepo->getAccessionsByBreedingInstitute();

        // for the ajax request
        if ($request->get('ajax')) {

            // selected country
            $accessionQtyCountry =  $countryRepo->getAccessionQtyCountry(
                $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabCountry = [];
            foreach ($accessionQtyCountry as $value) {
                # code...
                $bufferTabCountry[$value['id']] = $value['accQty'];
            }
            $accessionQtyCountry = $bufferTabCountry;
            

            // biological status
            $accessionQtyBiologicalStatus =  $biologicalStatusRepo->getAccessionQtyBiologicalStatus(
                $selectedCountries, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );
            
            $bufferTabBiologicalStat = [];
            foreach ($accessionQtyBiologicalStatus as $value) {
                # code...
                $bufferTabBiologicalStat[$value['id']] = $value['accQty'];
            }
            $accessionQtyBiologicalStatus = $bufferTabBiologicalStat;
            

            // mls status
            $accessionQtyMLSStatus =  $mlsStatusRepo->getAccessionQtyMLSStatus(
                $selectedCountries, $selectedBiologicalStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );
            
            $bufferTabMLSStat = [];
            foreach ($accessionQtyMLSStatus as $value) {
                # code...
                $bufferTabMLSStat[$value['id']] = $value['accQty'];
            }
            $accessionQtyMLSStatus = $bufferTabMLSStat;


            // collecting mission
            $accessionQtyColMission = $collectingMissionRepo->getAccessionQtyColMission(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabColMission = [];
            foreach ($accessionQtyColMission as $value) {
                # code...
                $bufferTabColMission[$value['id']] = $value['accQty'];
            }
            $accessionQtyColMission = $bufferTabColMission;


            // collecting source
            $accessionQtyColSource = $collectingSourceRepo->getAccessionQtyColSource(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabColSource = [];
            foreach ($accessionQtyColSource as $value) {
                # code...
                $bufferTabColSource[$value['id']] = $value['accQty'];
            }
            $accessionQtyColSource = $bufferTabColSource;


            // taxonomy
            $accessionQtyTaxonomy = $taxonomyRepo->getAccessionQtyTaxonomy(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabTaxonomy = [];
            foreach ($accessionQtyTaxonomy as $value) {
                # code...
                $bufferTabTaxonomy[$value['id']] = $value['accQty'];
            }
            $accessionQtyTaxonomy = $bufferTabTaxonomy;


            // maintaining institute
            $accessionQtyMainInstitute = $instituteRepo->getAccessionQtyMainInstitute(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabMainInstitute = [];
            foreach ($accessionQtyMainInstitute as $value) {
                # code...
                $bufferTabMainInstitute[$value['id']] = $value['accQty'];
            }
            $accessionQtyMainInstitute = $bufferTabMainInstitute;
            

            // donor institute
            $accessionQtyDonorInstitute = $instituteRepo->getAccessionQtyDonorInstitute(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources,
                $selectedMaintainingInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabDonorInstitute = [];
            foreach ($accessionQtyDonorInstitute as $value) {
                # code...
                $bufferTabDonorInstitute[$value['id']] = $value['accQty'];
            }
            $accessionQtyDonorInstitute = $bufferTabDonorInstitute;

            
            // bred institute
            $accessionQtyBredInstitute = $instituteRepo->getAccessionQtyBredInstitute(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources,
                $selectedMaintainingInstitutes, $selectedDonorInstitutes
            );

            $bufferTabBredInstitute = [];
            foreach ($accessionQtyBredInstitute as $value) {
                # code...
                $bufferTabBredInstitute[$value['id']] = $value['accQty'];
            }
            $accessionQtyBredInstitute = $bufferTabBredInstitute;

            // to filter accessions by criterias
            $filteredAccession = $accessionRepo->getAccessionAdvancedSearch(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );
        
            $context = [
                'title' => 'Accession Filtered List',
                'accessions' => $filteredAccession,
            ];
            
            return new JsonResponse([
                'content' => $this->renderView('search/content_accession.html.twig', $context),
                'accessionQtyCountry' => $accessionQtyCountry,
                'accessionQtyBiologicalStatus' => $accessionQtyBiologicalStatus,
                'accessionQtyMLSStatus' => $accessionQtyMLSStatus,
                'accessionQtyColMission' => $accessionQtyColMission,
                'accessionQtyColSource' => $accessionQtyColSource,
                'accessionQtyTaxonomy' => $accessionQtyTaxonomy,
                'accessionQtyMainInstitute'  => $accessionQtyMainInstitute,
                'accessionQtyDonorInstitute' => $accessionQtyDonorInstitute,
                'accessionQtyBredInstitute' => $accessionQtyBredInstitute
            ]);
        }

        $accessions =  $accessionRepo->findAll();
        $context = [
            'title' => 'Accession List',
            'accessions' => $accessions,
            // filters
            'accessionsByCountry' => $accessionsByCountry,
            'accessionsByBiologicalStatus' => $accessionsByBiologicalStatus,
            'accessionsByMLSStatus' => $accessionsByMLSStatus,
            'accessionsByTaxonomy' => $accessionsByTaxonomy,
            'accessionsByCollectingSource' => $accessionsByCollectingSource,
            'accessionsByCollectingMission' => $accessionsByCollectingMission,
            'accessionsByMaintainingInstitute' => $accessionsByMaintainingInstitute,
            'accessionsByDonorInstitute' => $accessionsByDonorInstitute,
            'accessionsByBreedingInstitute' => $accessionsByBreedingInstitute

        ];
        return $this->render('search/index_accession.html.twig', $context);
    }
}
