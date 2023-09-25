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

// set a class level route
 /**
 * @Route("brapi", name="brapi_")
 */
class GraphicalFilteringController extends AbstractController
{
    /**
     * @Route("/graphical/filtering", name="graphical_filtering")
     */
    public function graphicalFiltering(): Response
    {
        $context = [
            'title' => 'Graphical Filtering',
            'appEnv' => $_SERVER['APP_ENV']
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
