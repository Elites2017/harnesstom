<?php

namespace App\Controller;

use App\Entity\ObservationLevel;
use App\Entity\ObservationValue;
use App\Entity\TraitClass;
use App\Form\UploadFromExcelType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("observation/value/gourmetom", name="observation_value_gourmetom_")
 */
class ObservationValueGourmetomController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('observation_value_gourmetom/index.html.twig', [
            'controller_name' => 'ObservationValueGourmetomController',
        ]);
    }

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function uploadFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Setup repository of some entity
            $repoObservationValue = $entmanager->getRepository(ObservationValue::class);
            // Query how many rows are there in the ObservationValue table
            $totalObservationValueBefore = $repoObservationValue->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            // Return a number as response
            // e.g 972

            // get the file (name from the CountryUploadFromExcelType form)
            $file = $request->files->get('upload_from_excel')['file'];
            // set the folder to send the file to
            $fileFolder = __DIR__ . '/../../public/uploads/excel/';
            // apply md5 function to generate a unique id for the file and concat it with the original file name
            if ($file->getClientOriginalName()) {
                $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->addFlash('danger', "Fail to upload the file, try again");
                }
            } else {
                $this->addFlash('danger', "Error in the file name, try to rename the file and try again");
            }
            // read from the uploaded file
            $spreadsheet = IOFactory::load($fileFolder . $filePathName);
            // remove the first row (title) of the file
            $spreadsheet->getActiveSheet()->removeRow(1);
            // transform the uploaded file to an array
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            // loop over the array to get each row
            foreach ($sheetData as $key => $row) {
                $unitName = $row['A'];
                $obsVarId = $row['B'];
                $value = $row['C'];
                dd("Need to fix data loading columns ~ David PIERRE");
                // check if the file doesn't have empty columns
                if ($unitName != null && $obsVarId != null && $value != null) {
                    // check if the data is upload in the database
                    // $existingObservationValue = $entmanager->getRepository(ObservationValue::class)->findOneBy(['value' => $unitName]);
                    // // upload data only for objects that haven't been saved in the database
                    // if (!$existingObservationValue) {
                        $observationValue = new ObservationValue();
                        if ($this->getUser()) {
                            $observationValue->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $obsValUnitName = $entmanager->getRepository(ObservationLevel::class)->findOneBy(['unitname' => $unitName]);
                            if (($obsValUnitName != null) && ($obsValUnitName instanceof \App\Entity\ObservationLevel)) {
                                $observationValue->setUnitName($obsValUnitName);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unitname " .$unitName);
                        }

                        try {
                            //code...
                            $obsVariableTraitId = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $obsVarId]);
                            if (($obsVariableTraitId != null) && ($obsVariableTraitId instanceof \App\Entity\TraitClass)) {
                                $observationValue->setObservationVariableOriginal ($obsVariableTraitId->getObservationVariable());
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variable id " .$obsVarId);
                        }

                        try {
                            //code...
                            $observationValue->setValue($value);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation value " .$value);
                        }
                                                
                        $observationValue->setIsActive(true);
                        $observationValue->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($observationValue);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    // }
                }
            }

            // Query how many rows are there in the Country table
            $totalObservationValueAfter = $repoObservationValue->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalObservationValueBefore == 0) {
                $this->addFlash('success', $totalObservationValueAfter . " observation values have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalObservationValueAfter - $totalObservationValueBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new observation value has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation value has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation values have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('observation_value_gourmetom_index'));
        }

        $context = [
            'title' => 'Observation Value Upload From Excel',
            'observationValueUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('observation_value_gourmetom/upload_from_excel.html.twig', $context);
    }
}