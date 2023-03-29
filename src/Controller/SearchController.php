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
        // filters
        $accessionsByCountry = $countryRepo->getAccessionsByCountry();
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
        
        // to filter accessions by criterias
        $filteredAccession = $accessionRepo->getAccessionAdvancedSearch(
            $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
            $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
            $selectedDonorInstitutes, $selectedBreedingInstitutes
        );
        //dd($selectedBiologicalStatuses);
        
        // check if the coming query is from the filtering with ajax
        if ($request->get('ajax')) {
            $context = [
                'title' => 'Accession Filtered List',
                'accessions' => $filteredAccession
            ];
            return new JsonResponse([
                'content' => $this->renderView('search/content_accession.html.twig', $context)
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
