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
        // $accessionsByCountry = $countryRepo->getAccessionsByCountry($selectedBiologicalStatuses);
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

        
        
        // to filter accessions by criterias
        // $filteredAccession = $accessionRepo->getAccessionAdvancedSearch(
        //     $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
        //     $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
        //     $selectedDonorInstitutes, $selectedBreedingInstitutes
        // );
        //dd($selectedBiologicalStatuses);
        
        // check if the coming query is from the filtering with ajax
        // if ($request->get('ajax')) {
        //     $context = [
        //         'title' => 'Accession Filtered List',
        //         'accessions' => $filteredAccession
        //     ];
        //     return new JsonResponse([
        //         'content' => $this->renderView('search/content_accession.html.twig', $context)
        //     ]);
        // }

        $accessionQtyCountry =  $countryRepo->getAccessionQtyCountry($selectedBiologicalStatuses);
        // create a new custom table
        // $bufferTab = [];
        // foreach ($accessionQtyCountry as $value) {
        //     //dd($value);
        //     # code...
        //     $bufferTab[$value['id']] =  $value['accQty'];
        // }
        // $accessionQtyCountry = $bufferTab;
        //dd($accessionQtyCountry);
        

        // $selectedCountries = $request->get("countries");
        // $selectedBiologicalStatuses = $request->get("biologicalStatuses");
        // $selectedMLSStatuses = $request->get('mlsStatuses');
        // $selectedTaxonomies = $request->get('taxonomies');
        // $selectedCollectingMissions = $request->get('collectingMissions');
        // $selectedCollectingSources = $request->get('collectingSources');
        // // institutes
        // $selectedMaintainingInstitutes = $request->get('maintainingInstitutes');
        // $selectedDonorInstitutes = $request->get('donorInstitutes');
        // $selectedBreedingInstitutes = $request->get('breedingInstitutes');

        if ($request->get('ajax')) {
            // get the filters selected by the user
            // $selectedCountries = $request->get("countries");
            // $selectedBiologicalStatuses = $request->get("biologicalStatuses");
            // $selectedMLSStatuses = $request->get('mlsStatuses');
            // $selectedTaxonomies = $request->get('taxonomies');
            // $selectedCollectingMissions = $request->get('collectingMissions');
            // $selectedCollectingSources = $request->get('collectingSources');
            // // institutes
            // $selectedMaintainingInstitutes = $request->get('maintainingInstitutes');
            // $selectedDonorInstitutes = $request->get('donorInstitutes');
            // $selectedBreedingInstitutes = $request->get('breedingInstitutes');

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
                $selectedCountries, $selectedBiologicalStatuses, $selectedTaxonomies,
                $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );

            $bufferTabColMission = [];
            foreach ($accessionQtyColMission as $value) {
                # code...
                $bufferTabColMission[$value['id']] = $value['accQty'];
            }
            $accessionQtyColMission = $bufferTabColMission;

            //dd($accessionQtyColMission);
        

            
            

            // $newArrBuf = [];
            // foreach ($accessionsByCountry as $value) {
            //     # code...
            //     $newArrBuf[$value['id']] = ["id" => $value['id'], "iso3" => $value['iso3'], "accQty" => $value["accQty"] ];
            // }
            // $accessionQtyCountry = $newArrBuf;
            //dd($newArrBuf);

            // filters
            //$accessionsByCountryNumber = $countryRepo->getAccessionsByCountry($selectedBiologicalStatuses);

            //$accessionsByCountry = $countryRepo->getAccessionCountries();
            //dd($accessionsByCountry);
            

            // $accessionsByBiologicalStatus = $biologicalStatusRepo->getAccessionsByBiologicalStatus();
            // $accessionsByMLSStatus = $mlsStatusRepo->getAccessionsByMLSStatus();
            // $accessionsByTaxonomy = $taxonomyRepo->getAccessionsByTaxonomy();
            // //$accessionsBySpecies = $taxonomyRepo->getAccessionsBySpecies();
            // $accessionsByCollectingSource = $collectingSourceRepo->getAccessionsByCollectingSource();
            // $accessionsByCollectingMission = $collectingMissionRepo->getAccessionsByCollectingMission();
            // // institutes
            // $accessionsByMaintainingInstitute = $instituteRepo->getAccessionsByMaintainingInstitute();
            // $accessionsByDonorInstitute = $instituteRepo->getAccessionsByDonorInstitute();
            // $accessionsByBreedingInstitute = $instituteRepo->getAccessionsByBreedingInstitute();
            
            // to filter accessions by criterias
            $filteredAccession = $accessionRepo->getAccessionAdvancedSearch(
                $selectedCountries, $selectedBiologicalStatuses, $selectedMLSStatuses, $selectedTaxonomies,
                $selectedCollectingMissions, $selectedCollectingSources, $selectedMaintainingInstitutes,
                $selectedDonorInstitutes, $selectedBreedingInstitutes
            );
        
            $context = [
                'title' => 'Accession Filtered List',
                'accessions' => $filteredAccession,
                // 'accessionsByCountry' => $accessionsByCountry,
                //'accessionQtyCountry' => $accessionQtyCountry,
                // 'accessionsByBiologicalStatus' => $accessionsByBiologicalStatus,
                // 'accessionsByMLSStatus' => $accessionsByMLSStatus,
                // 'accessionsByTaxonomy' => $accessionsByTaxonomy,
                // 'accessionsByCollectingSource' => $accessionsByCollectingSource,
                // 'accessionsByCollectingMission' => $accessionsByCollectingMission,
                // 'accessionsByMaintainingInstitute' => $accessionsByMaintainingInstitute,
                // 'accessionsByDonorInstitute' => $accessionsByDonorInstitute,
                // 'accessionsByBreedingInstitute' => $accessionsByBreedingInstitute
            ];
            
            return new JsonResponse([
                'content' => $this->renderView('search/content_accession.html.twig', $context),
                'accessionQtyCountry' => $accessionQtyCountry,
                'accessionQtyBiologicalStatus' => $accessionQtyBiologicalStatus,
                'accessionQtyMLSStatus' => $accessionQtyMLSStatus,
                'accessionQtyColMission' => $accessionQtyColMission
            ]);
        }


        // SELECT country.id, count(acc.id)
        // FROM country, biological_status bs
        // LEFT JOIN accession acc
        // ON country.id = acc.origcty_id
        // WHERE bs.id = acc.sampstat_id AND bs.id = 6
        // GROUP By country.id Order By count(acc.id) DESC; 

        $accessions =  $accessionRepo->findAll();
        $context = [
            'title' => 'Accession List',
            'accessions' => $accessions,
            // filters
            'accessionsByCountry' => $accessionsByCountry,
            //'accessionQtyCountry' => $accessionQtyCountry,
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
