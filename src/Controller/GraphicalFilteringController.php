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
     * @Route("/phenotypes-search", name="phenotype_search", methods={"POST"})
     */
    public function phenotypes_search(Request $request, StudyRepository $studyRepo, ObservationValueOriginalRepository $obsValOriRepo): Response
    {
        // get the params
        // $study = $request->request->get('brapi-form');
        // dd("Req ", $study);
        $studySelected = $studyRepo->findOneBy(["id"=>"15"]);
        //dd(count($studySelected->getObservationVariableDbIds()));
        $obsLevels = $studySelected->getObservationLevels();
        $obsValues = [];
        $oneObsValOri = [];
        foreach ($obsLevels as $key => $oneObsLevel) {
            # code...
            $oneObsValOri [] = $obsValOriRepo->findBy(["unitName" => $oneObsLevel->getId()]);
            // foreach ($oneObsValOri as $keyo => $oneSingleObsValOri) {
            //     # code...
            //     $obsValues [] = [
            //         "observationVariableName" => $oneSingleObsValOri->getObservationVariableOriginal()->getName(),
            //         "value" => $oneSingleObsValOri->getValue()
            //     ];
            // }  
        }

        foreach ($oneObsValOri as $keyo => $oneSingleObsValOri) {
            # code...
            //dd(count($oneSingleObsValOri));
            foreach ($oneSingleObsValOri as $key => $newTab) {
                # code...
                $obsValues [] = [
                    [
                        "observationVariableName" => $newTab->getObservationVariableOriginal()->getName(),
                        "value" => $newTab->getValue()
                    ],
                ];
            }
            
        }
       // dd(count($obsValues));

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
        
        return new JsonResponse([
            'data' => [
                'morning' => "good morning",
                'evening' => "good night",
            ],
            'result' => [
                'data' => "result of the data",
                'germplasmName' => "good morning",
                'observationUnitName' => "good night",
                'studyName' =>$studySelected->getAbbreviation(),
                'observations' => $obsValues
                
            ]

        ]);
    }

}