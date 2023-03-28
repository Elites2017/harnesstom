<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AccessionRepository;
use App\Repository\CountryRepository;
use App\Repository\BiologicalStatusRepository;
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
    public function index(AccessionRepository $accessionRepo, CountryRepository $countryRepo, BiologicalStatusRepository $biologicalStatusRepo, Request $request): Response
    {
        // filters
        $accessionsByCountry = $countryRepo->getAccessionsByCountry();
        $accessionsByBiologicalStatus = $biologicalStatusRepo->getAccessionsByBiologicalStatus();
        
        // get the filters selected by the user
        $selectedCountries = $request->get("countries");
        //dd($selectedCountries);
        $selectedBiologicalStatuses = $request->get("biologicalStatuses");
        
        $filteredAccession = $accessionRepo->getAccessionAdvancedSearch(
            $selectedCountries, $selectedBiologicalStatuses
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
            'accessionsByBiologicalStatus' => $accessionsByBiologicalStatus
        ];
        return $this->render('search/index_accession.html.twig', $context);
    }
}
