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
     * @Route("/phenotypes-search", name="phenotype_search", methods={"POST", "GET"})
     */
    public function phenotypesSearch(Request $request, StudyRepository $studyRepo, ObservationValueOriginalRepository $obsValOriRepo): Response
    {
        // get the params
        // $study = $request->request->get('brapi-form');
        //var_dump("Req ", $request->get('studyDbIds'));
        $studyProvided = $request->get('study');
        //dd($studyProvided);
        $studySelected = $studyRepo->findOneBy(["id"=>15]);
        //dd("Study - ", $studySelected->getBraApiObservationLevels()["levelName"]);
        //dd(count($studySelected->getObservationVariableDbIds()));
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
            $obsUnitsAndValues[] = $obsValuesByUnit; 
        }

        //dd($obsUnitsAndValues);
        $myDat = [];
        foreach ($obsUnitsAndValues as $key => $one) {
            # code...
            //dd($obsUnitsAndValues);
            $myDat [] = 
            [
                "studyLocationDbId" => "string",
                "studyDbId" => "string",
                "germplasmDbId" => "string",
                "germplasmName" => "string",
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
                "observationUnitName" => "string",
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
                "observations" => $one,
                "Y" => "string",
                "X" => "string"
            ];
        }

        //dd($myDat);

        return new JsonResponse([
            'result' => 
            [
                "data" => $myDat
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

//dd($newTest);
        //$ooo = [];
        //$temp = [];
        // foreach ($allObsValOri as $key => $justOne) {
        //     # code...
        //     foreach ($justOne as $keyL => $one) {
        //         # code...
        //         $temp = [
        //             "observationVariableName" => $one->getObservationVariableOriginal()->getName(),
        //             "value" => $one->getValue()

        //         ];
        //     }
        //     //dd($justOne->getObservationVariableOriginal()->getName());
        //     $ooo [] = $temp;
        //     //dd("ooo", $ooo);
        // }

//dd($ooo);
        // $returnedVal = [];

        // $allObsVar = [];
        // $allStudies = [];
        // $allObs = [];

        // foreach ($oneObsValOri as $keyo => $oneSingleObsValOri) {
        //     # code...
        //     dd(count($oneSingleObsValOri));
        //     $allObsVar [] =  $oneSingleObsValOri;
        //     $allStudies [] = $studySelected->getAbbreviation();
        //     $allObs [] = [
        //         "observationVariableName" => $oneSingleObsValOri[$keyo]->getObservationVariableOriginal()->getName(),
        //         "value" => $oneSingleObsValOri[$keyo]->getValue()
        //     ];

            //dd(count($oneSingleObsValOri));
            // foreach ($oneSingleObsValOri as $key => $newTab) {
            //     # code...

            //     $returnedVal [] = [
            //         'studyName' => [$studySelected->getAbbreviation()],
            //         'observations' => $obsValues
            //     ];

            //     $obsValues [] = [
            //         [
            //             "observationVariableName" => $newTab->getObservationVariableOriginal()->getName(),
            //             "value" => $newTab->getValue()
            //         ],
            //     ];
            // }
            
        // }
       //dd($allObs);

        // foreach ($obsLevels as $key => $oneObsLevel) {
        //     # code...
        //     $obsVaroriginals [] = $obsValOriRepo->findBy(["unitName" => $oneObsLevel->getId()]);
            
        // }

        // $testK = [];
        // foreach ($obsVaroriginals as $key => $onebsVarOri) {
        //     # code...
        //     $testK [] = $onebsVarOri;
        //     //dd(count($obsVaroriginals));
        //     if($onebsVarOri[$key] <= 7) {
        //         $obsValues [] = [
        //             "observationvariableName" => $onebsVarOri[$key]->getObservationVariableOriginal()->getName(),
        //             "value" => $onebsVarOri->getValue()
        //         ];
        //     }
        // }
        // dd($testK);
        

        

    //     /**
    //  * @Groups({"study:read"})
    //  */
    // public function getObservationVariableDbIds() {
    //     $unitNames = [];
    //     $obsValues = [];
    //     $variableIds = [];
    //     foreach ($this->observationLevels as $oneObsLevel) {
    //         # code...
    //         $unitNames [] = $oneObsLevel->getUnitname();
    //         $obsValues [] = $oneObsLevel->getObservationValueOriginals();
    //     }

    //     foreach ($obsValues as $key => $oneObsValue) {
    //         # code...
    //         if ($oneObsValue[$key] !== null) {
    //             $variableIds [] = $oneObsValue[$key]->getObservationVariableOriginal()->getName();
    //         }
    //     }
    //     return $variableIds;
    // }
    // return new JsonResponse([
    //     'content' => $this->renderView('gourmetom/content_accession.html.twig', $context)
    // ]);


// 'germplasmName' => [["Acce 1"], ["Acce 2"]],
                // 'observationUnitName' => [["Unit 1"], ["Unit 2"],],
                // 'studyName' => $studySelected->getAbbreviation(),
                // 'observations' => $obsValues
                // 'observations' => [
                //     [
                //         "observationVariableName" => "My Name",
                //         "value" => 78
                //     ],
                //     [
                //         "observationVariableName" => "My 2Name",
                //         "value" => 738
                //     ],
                //     [
                //         "observationVariableName" => "My 3 Name",
                //         "value" => 337
                //     ],
                //     [
                //         "observationVariableName" => "My new Name",
                //         "value" => 78
                //     ],
                //     [
                //         "observationVariableName" => "My new 2 Name",
                //         "value" => 738
                //     ],
                //     [
                //         "observationVariableName" => "My new 3 Name",
                //         "value" => 337
                //     ],
                //     [
                //         "observationVariableName" => "My new x Name",
                //         "value" => 80
                //     ],
                //     [
                //         "observationVariableName" => "My Y Name",
                //         "value" => 444
                //     ],
                // ],