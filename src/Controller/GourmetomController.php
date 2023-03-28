<?php

namespace App\Controller;

use App\Repository\AccessionRepository;
use App\Repository\BiologicalStatusRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gourmetom", name="gourmetom_")
 */
class GourmetomController extends AbstractController
{
    /**
     * @Route("/accession", name="accession")
     */
    public function index(AccessionRepository $accessionRepo, CountryRepository $countryRepo, BiologicalStatusRepository $biologicalStatusRepo, Request $request): Response
    {
        // filters
        $countries = $countryRepo->getAccessionCountries();
        $biologicalStatuses = $biologicalStatusRepo->getAccessionBiologicalStatuses();
        
        // get the filters selected by the user
        $selectedCountries = $request->get("countries");
        $selectedBiologicalStatuses = $request->get("biologicalStatuses");
        $selectedFruitWeightGrams = $request->get("fwgs");
        $selectedShapes = $request->get("shapes");
        $selectedFasciation = $request->get("fruitf");
        $selectedShoulderShape = $request->get("fruitSS");
        $selectedFColor = $request->get("fruitColors");
        $selectedGreenSI = $request->get("greenSI");
        $selectedPuffinessA = $request->get("puffinessA");
        $selectedPericarpT = $request->get("pericarpT");
        $selectedFruitFirmness = $request->get("fruitFirmness");
        $selectedBrix = $request->get("brix");
        $selectedLoad = $request->get("load");
        
        $filteredAccession = $accessionRepo->getAccessionFilteredOrNot(
            $selectedCountries, $selectedBiologicalStatuses, $selectedFruitWeightGrams,
            $selectedShapes, $selectedFasciation, $selectedShoulderShape,
            $selectedFColor, $selectedGreenSI, $selectedPuffinessA,
            $selectedPericarpT, $selectedFruitFirmness, $selectedBrix, $selectedLoad
        );
        //dd($selectedBiologicalStatuses);
        
        // check if the coming query is from the filtering with ajax
        if ($request->get('ajax')) {
            $context = [
                'title' => 'Accession Filtered List',
                'accessions' => $filteredAccession
            ];
            return new JsonResponse([
                'content' => $this->renderView('gourmetom/content_accession.html.twig', $context)
            ]);
        }

        $accessions =  $accessionRepo->findAll();
        $context = [
            'title' => 'Accession List',
            'accessions' => $accessions,
            // filters
            'countries' => $countries,
            'biologicalStatuses' => $biologicalStatuses
        ];
        return $this->render('gourmetom/index_accession.html.twig', $context);
    }
}
