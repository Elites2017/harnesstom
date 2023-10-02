<?php

namespace App\Controller;

use App\Entity\Study;
use App\Repository\ObservationValueOriginalRepository;
use App\Repository\StudyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// As of October 2nd 2023, this controller is aimed to render / process all the brapi
// related third party apps.

// In the case of the graphical filtering, 
// 1. the graphicalFiltering() renders a twig template which is waiting from the user some
// input value as paramaters. Once the paramaters are set and the user sends the request,

// 2. A javascript code in the same rendered twig template sends a request to the BrAPI.js
// file to link the request with the POST phenotypesSearch method in the BrAPI and that method
// itself calls phenotypesSearch() in this controller to process the request.

// 3. The phenotypesSearch() in this controller decodes the content of the request to get
// the paramaters sent by user as the parameters are sent on json encode in the request content.
// Then a query is sent to the DB to get the study related based on its id or its abbreviation.
// From the study related (selected), the realted observationLevels list is retrived. Furthermore
// A loop has been performed on that list in order to get observationValueOriginal based on the unitname
// for each element of the observationLevels list. The observationValueOriginal is a list of each
// observationLevel, which means for each observationLevel there is a list of the observationValueOriginal.
// From those lists, another loop is performed on them to build and render the data (observations)
// which have to be sent to the graphical filtering js code in the graphical filtering twig template.
// As the observations are a list, they are added in an array called obsValuesByUnit which itself
// is added to the returned list of observations, unitname, germplasm... called obsUnitsAndValues.

// 4. Finally another loop is performed on the returned obsUnitsAndValues list to build the exact
// data structure waiting by the graphical filtering js code in the graphical filteringctwig template.


// set a class level route
 /**
 * @Route("brapi", name="brapi_")
 */
class BrapiController extends AbstractController
{
    /**
     * @Route("/graphical/filtering", name="graphical_filtering")
     */
    public function graphicalFiltering(): Response
    {
        $context = [
            'title' => 'Graphical Filtering',
        ];
        return $this->render('brapi/graphical_filtering.html.twig', $context);
    }

    /**
     * @Route("/phenotypes-search", name="phenotypes_search", methods={"POST"})
     */
    public function phenotypesSearch(Request $request, StudyRepository $studyRepo, ObservationValueOriginalRepository $obsValOriRepo): Response
    {
        // get the params
        // use json decode, because the params sent to this controller is in json
        $paramsSent = json_decode($request->getContent(), true);
        $studyIdAbbreviation = $paramsSent["studyDbIds"];
        $observationLevel = $paramsSent["observationLevel"];
        $studySelected = "";
        // querying the db to get the study
        if ($studyRepo->findOneBy(["id" => $studyIdAbbreviation]) != null) {
            $studySelected = $studyRepo->findOneBy(["id" => $studyIdAbbreviation]);
        } else {
            $studySelected = $studyRepo->findOneBy(["abbreviation" => $studyIdAbbreviation]);
        }
        // test if one correct study was found
        if ($studySelected != null) {
            $obsLevels = $studySelected->getObservationLevels();
            $obsUnitsAndValues = [];
            foreach ($obsLevels as $key => $oneObsLevel) {
                # code...
                $oneObsValOri = $obsValOriRepo->findBy(["unitName" => $oneObsLevel->getId()]);
                $obsValuesByUnit = [];
                foreach ($oneObsValOri as $keyo => $oneSingleObsValOri) {
                    # code...
                    $obsValuesByUnit [] = [
                            "observationVariableName" => $oneSingleObsValOri->getObservationVariableOriginal()->getName(),
                            "value" => $oneSingleObsValOri->getValue()
                        
                        ];
                } 
                $obsUnitsAndValues[] = [
                    "obsValuesByUnit" => $obsValuesByUnit,
                    "germplasmName" => $oneObsLevel->getGermaplasm()->getAccession()->getAccename(),
                    "germplasmId" => $oneObsLevel->getGermaplasm()->getAccession()->getId(),
                    "unitName" => $oneObsLevel->getUnitname(),
                ]; 
            }

            $returnedData = [];
            foreach ($obsUnitsAndValues as $key => $oneOBU) {
                # code...
                $returnedData [] = 
                [
                    "studyLocationDbId" => "string",
                    "studyDbId" => "string",
                    "germplasmDbId" => $oneOBU["germplasmId"],
                    "germplasmName" => $oneOBU["germplasmName"],
                    "observationLevel" => "string",
                    "observationUnitXref" => 
                    [
                    [
                        "id" => "string",
                        "source" => "string"
                    ]
                    ],
                    "programDbId" => "string",
                    "observationUnitDbId" => "string",
                    "observationUnitName" => $oneOBU["unitName"],
                    "observationLevels" => "string",
                    "plotNumber" => "string",
                    "plantNumber" => "string",
                    "blockNumber" => "string",
                    "replicate" => "string",
                    "entryType" => "string",
                    "entryNumber" => "string",
                    "studyName" => $studySelected->getAbbreviation(),
                    "studyLocation" => "string",
                    "programName" => "string",
                    "treatments" => 
                    [
                    [
                        "modality" => "string",
                        "factor" => "string"
                    ]
                    ],
                    "observations" => $oneOBU["obsValuesByUnit"],
                    "Y" => "string",
                    "X" => "string"
                ];
            }

            return new JsonResponse([
                'result' => 
                [
                    "data" => $returnedData
                    ],
                    "metadata" => 
                    [
                    "status" => 
                    [
                        [
                        "name" => "string",
                        "code" => "string"
                        ]
                    ],
                    "datafiles" => 
                    [
                        "string"
                    ],
                    "pagination" => 
                    [
                        "totalCount" => 0,
                        "pageSize" => 0,
                        "currentPage" => 0,
                        "totalPages" => 0
                    ]
                    ],
                ]

            );

        }
    }

    /**
     * @Route("/phenotypesSearch", name="phenotypesSearch", )
     */
    public function index(): Response
    {
        return new JsonResponse([
            "studyComparison" => "TEST"
        ]);
    }

}
