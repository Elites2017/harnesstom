<?php

namespace App\Controller;

use App\Repository\AccessionRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search", name="search_")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/accession", name="accession")
     */
    public function index(AccessionRepository $accessionRepo, CountryRepository $countryRepo, Request $request): Response
    {
        // filters
        $countries = $countryRepo->getAccessionCountries();
        
        // get the filters selected by the user
        $selectedFilters = $request->get("countries");
        $filteredAccession = $accessionRepo->getAccessionFilteredOrNot($selectedFilters);
        //dd($filteredAccession);
        
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
            'countries' => $countries
        ];
        return $this->render('search/index_accession.html.twig', $context);
    }
}
