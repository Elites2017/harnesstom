<?php

namespace App\Controller;

use App\Entity\Germplasm;
use App\Entity\ObservationLevel;
use App\Entity\Study;
use App\Form\ObservationLevelType;
use App\Form\ObservationLevelUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ObservationLevelRepository;
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
 * @Route("observation/level", name="observation_level_")
 */
class ObservationLevelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationLevelRepository $observationLevelRepo): Response
    {
        $observationLevels = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $observationLevels = $observationLevelRepo->findAll();
            } else {
                $observationLevels = $observationLevelRepo->findReleasedTrialStudyObsLevel($this->getUser());
            }
        } else {
            $observationLevels = $observationLevelRepo->findReleasedTrialStudyObsLevel();
        }
        $context = [
            'title' => 'Observation Level List',
            'observationLevels' => $observationLevels
        ];
        return $this->render('observation_level/index.html.twig', $context);
    }

    /**
     * @Route("/datatable", name="datatable")
     */
    public function datatable(Datatable $datatableService, ObservationLevelRepository $observationLevelRepo, Request $request)
    {
        $datatableRes = $datatableService->getDatatable($observationLevelRepo, $request);
        return $datatableRes;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationLevel = new ObservationLevel();
        $form = $this->createForm(ObservationLevelType::class, $observationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationLevel->setCreatedBy($this->getUser());
            }
            $observationLevel->setIsActive(true);
            $observationLevel->setCreatedAt(new \DateTime());
            $entmanager->persist($observationLevel);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('observation_level_index'));
        }

        $context = [
            'title' => 'Observation Level Creation',
            'observationLevelForm' => $form->createView()
        ];
        return $this->render('observation_level/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationLevel $observationLevelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Level Details',
            'observationLevel' => $observationLevelSelected
        ];
        return $this->render('observation_level/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationLevel $observationLevel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_level_edit', $observationLevel);
        $form = $this->createForm(ObservationLevelUpdateType::class, $observationLevel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $observationLevel->setLastUpdated(new \DateTime());
            $entmanager->persist($observationLevel);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('observation_level_index'));
        }

        $context = [
            'title' => 'Observation Level Update',
            'observationLevelForm' => $form->createView()
        ];
        return $this->render('observation_level/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationLevel $observationLevel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationLevel->getId()) {
            $observationLevel->setIsActive(!$observationLevel->getIsActive());
        }
        $entmanager->persist($observationLevel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationLevel->getIsActive()
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
            $repoObservationLevel = $entmanager->getRepository(ObservationLevel::class);
            // Query how many rows are there in the ObservationLevel table
            $totalObservationLevelBefore = $repoObservationLevel->createQueryBuilder('tab')
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
                    $this->addFlash('danger', "Fail to upload the file, try again ");
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
                $studyAbbreviation = $row['A'];
                $germplasmID = $row['B'];
                $observationUnitName = $row['C'];
                $observationLevelName = $row['D'];
                $blockNumber = $row['E'];
                $subBlockNumber = $row['F'];
                $plotNumber = $row['G'];
                $plantNumber = $row['H'];
                $replicate = $row['I'];
                $observationUnitPosition = $row['J'];
                $positionCoordX = $row['K'];
                $positionCoordXType = $row['L'];
                $positionCoordY = $row['M'];
                $positionCoordYType = $row['N'];
                $date = $row['O'];
                // check if the file doesn't have empty columns
                if ($observationUnitName != null && $studyAbbreviation != null) {
                    // check if the data is upload in the database
                    $existingObservationLevel = $entmanager->getRepository(ObservationLevel::class)->findOneBy(['unitname' => $observationUnitName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingObservationLevel) {
                        $observationLevel = new ObservationLevel();
                        if ($this->getUser()) {
                            $observationLevel->setCreatedBy($this->getUser());
                        }
                        
                        try {
                            //code...
                            $observationLevelStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                            if (($observationLevelStudy != null) && ($observationLevelStudy instanceof \App\Entity\Study)) {
                                $observationLevel->setStudy($observationLevelStudy);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study abbreaviation " .$studyAbbreviation);
                        }

                        try {
                            //code...
                            $observationLevelGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmID]);
                            if (($observationLevelGermplasm != null) && ($observationLevelGermplasm instanceof \App\Entity\Germplasm)) {
                                $observationLevel->setGermaplasm($observationLevelGermplasm);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the germplasm ID " .$germplasmID);
                        }

                        try {
                            //code...
                            $observationLevel->setName($observationLevelName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation level name " .$observationLevelName);
                        }

                        try {
                            //code...
                            if ($observationUnitName) {
                                $observationLevel->setUnitName($observationUnitName);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation unit name " .$observationUnitName);
                        }

                        try {
                            //code...
                            if ($blockNumber) {
                                $observationLevel->setBlockNumber($blockNumber);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the block number " .$blockNumber);
                        }

                        try {
                            //code...
                            if ($subBlockNumber) {
                                $observationLevel->setSubBlockNumber($subBlockNumber);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the sub block number " .$subBlockNumber);
                        }

                        try {
                            //code...
                            if ($plotNumber) {
                                $observationLevel->setPlotNumber($plotNumber);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the plot number " .$plotNumber);
                        }

                        try {
                            //code...
                            if ($plantNumber) {
                                $observationLevel->setPlantNumber($plantNumber);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the plant number " .$plantNumber);
                        }

                        try {
                            //code...
                            if ($replicate) {
                                $observationLevel->setReplicate($replicate);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the replicate " .$replicate);
                        }

                        try {
                            //code...
                            if ($observationUnitPosition) {
                                $observationLevel->setUnitPosition($observationUnitPosition);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit position " .$observationUnitPosition);
                        }

                        try {
                            //code...
                            if ($positionCoordX) {
                                $observationLevel->setUnitCoordinateX($positionCoordX);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit coordinate X " .$positionCoordX);
                        }

                        try {
                            //code...
                            if ($positionCoordY) {
                                $observationLevel->setUnitCoordinateY($positionCoordY);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit coordinate Y " .$positionCoordY);
                        }

                        try {
                            //code...
                            if ($positionCoordXType) {
                                $observationLevel->setUnitCoordinateXType($positionCoordXType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit coordinate XType " .$positionCoordXType);
                        }

                        try {
                            //code...
                            if ($positionCoordYType) {
                                $observationLevel->setUnitCoordinateYType($positionCoordYType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit coordinate YType " .$positionCoordYType);
                        }

                        $observationLevel->setIsActive(true);
                        $observationLevel->setCreatedAt(new \DateTime());
                        //dd($observationLevel);
                        try {
                            //code...
                            $entmanager->persist($observationLevel);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalObservationLevelAfter = $repoObservationLevel->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalObservationLevelBefore == 0) {
                $this->addFlash('success', $totalObservationLevelAfter . " observation levels have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalObservationLevelAfter - $totalObservationLevelBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new observation level has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation level has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation levels have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('observation_level_index'));
        }

        $context = [
            'title' => 'ObservationLevel Upload From Excel',
            'observationLevelUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('observation_level/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/observation_level_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'observation_level_template_example.xlsx');
        return $response;
       
    }
}
