<?php

namespace App\Controller;

use App\Entity\ObservationLevel;
use App\Entity\ObservationValueOriginal;
use App\Entity\ObservationVariable;
use App\Entity\TraitClass;
use App\Form\ObservationValueOriginalType;
use App\Form\ObservationValueOriginalUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ObservationValueOriginalRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("observation/value", name="observation_value_")
 */
class ObservationValueOriginalController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationValueOriginalRepository $observationValueRepo): Response
    {
        $observationValues = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $observationValues = $observationValueRepo->findAll();
            } else {
                $observationValues = $observationValueRepo->findReleasedTrialStudyObsLevelObsValues($this->getUser());
            }
        } else {
            $observationValues = $observationValueRepo->findReleasedTrialStudyObsLevelObsValues();
        }
        $context = [
            'title' => 'Observation Value List',
            'observationValues' => $observationValues
        ];
        return $this->render('observation_value/index.html.twig', $context);
    }

    /**
     * @Route("/datatable", name="datatable")
     */
    public function datatable(Datatable $datatableService, ObservationValueOriginalRepository $observationValueRepo, Request $request)
    {
        $datatableRes = $datatableService->getDatatable($observationValueRepo, $request);
        return $datatableRes;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationValue = new ObservationValueOriginal();
        $form = $this->createForm(ObservationValueOriginalType::class, $observationValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationValue->setCreatedBy($this->getUser());
            }
            $observationValue->setIsActive(true);
            $observationValue->setCreatedAt(new \DateTime());
            $entmanager->persist($observationValue);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('observation_value_index'));
        }

        $context = [
            'title' => 'Observation Value Creation',
            'observationValueForm' => $form->createView()
        ];
        return $this->render('observation_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationValueOriginal $observationValueSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Value Details',
            'observationValue' => $observationValueSelected
        ];
        return $this->render('observation_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationValueOriginal $observationValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_value_edit', $observationValue);
        $form = $this->createForm(ObservationValueOriginalUpdateType::class, $observationValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationValue);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('observation_value_index'));
        }

        $context = [
            'title' => 'Observation Value Update',
            'observationValueForm' => $form->createView()
        ];
        return $this->render('observation_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationValueOriginal $observationValue, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationValue->getId()) {
            $observationValue->setIsActive(!$observationValue->getIsActive());
        }
        $entmanager->persist($observationValue);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationValue->getIsActive()
        ], 200);
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
            $repoObservationValue = $entmanager->getRepository(ObservationValueOriginal::class);
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
                // check if the file doesn't have empty columns
                if ($unitName !== null && $obsVarId !== null && $value !== null) {
                    $obsValUnitName = $entmanager->getRepository(ObservationLevel::class)->findOneBy(['unitname' => $unitName]);
                    $obsVariableTraitId = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $obsVarId]);
                    if ($obsValUnitName && $obsVariableTraitId){
                        // check if the data is upload in the database
                        $existingObservationValue = $entmanager->getRepository(ObservationValueOriginal::class)->findOneBy([
                            'unitName' => $obsValUnitName->getId(),
                            'observationVariableOriginal' => $obsVariableTraitId->getObservationVariable()->getId(),
                            'value' => $value]);
                            
                        // upload data only for objects that haven't been saved in the database
                        if (!$existingObservationValue) {
                            $observationValue = new ObservationValueOriginal();
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
                        }
                    } else {
                        $this->addFlash('danger', "Either the observation level " .$unitName. " does not exist or the trait ontology " .$obsVarId. " does not have observation variable ");
                    }
                    
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
            return $this->redirect($this->generateUrl('observation_value_index'));
        }

        $context = [
            'title' => 'Observation Value Upload From Excel',
            'observationValueUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('observation_value/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/observation_value_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'observation_value_template_example.xlsx');
        return $response;
    }
}