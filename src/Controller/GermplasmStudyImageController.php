<?php

namespace App\Controller;

use App\Entity\AnatomicalEntity;
use App\Entity\DevelopmentalStage;
use App\Entity\FactorType;
use App\Entity\Germplasm;
use App\Entity\GermplasmStudyImage;
use App\Entity\Study;
use App\Form\GermplasmStudyImageType;
use App\Form\GermplasmStudyImageUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GermplasmStudyImageRepository;
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
 * @Route("/germplasm/study/image", name="germplasm_study_image_")
 */
class GermplasmStudyImageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GermplasmStudyImageRepository $germplasmStudyImageRepo): Response
    {
        $germplasmStudyImages = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $germplasmStudyImages = $germplasmStudyImageRepo->findAll();
            } else {
                $germplasmStudyImages = $germplasmStudyImageRepo->findReleasedTrialStudyGermplasmStudyImage($this->getUser());
            }
        } else {
            $germplasmStudyImages = $germplasmStudyImageRepo->findReleasedTrialStudyGermplasmStudyImage();
        }
        $context = [
            'title' => 'Germplasm Study Image List',
            'germplasmStudyImages' => $germplasmStudyImages
        ];
        return $this->render('germplasm_study_image/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $germplasmStudyImage = new GermplasmStudyImage();
        $form = $this->createForm(GermplasmStudyImageType::class, $germplasmStudyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $germplasmStudyImage->setCreatedBy($this->getUser());
            }
            $germplasmStudyImage->setIsActive(true);
            $germplasmStudyImage->setCreatedAt(new \DateTime());
            $entmanager->persist($germplasmStudyImage);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('germplasm_study_image_index'));
        }

        $context = [
            'title' => 'Germplasm Study Image Creation',
            'germplasmStudyImageForm' => $form->createView()
        ];
        return $this->render('germplasm_study_image/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GermplasmStudyImage $studieSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Germplasm Study Image Details',
            'study' => $studieSelected
        ];
        return $this->render('germplasm_study_image/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GermplasmStudyImage $germplasmStudyImage, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('study_edit', $germplasmStudyImage);
        $form = $this->createForm(GermplasmStudyImageUpdateType::class, $germplasmStudyImage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($germplasmStudyImage);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('germplasm_study_image_index'));
        }

        $context = [
            'title' => 'Germplasm Study Image Update',
            'germplasmStudyImageForm' => $form->createView()
        ];
        return $this->render('germplasm_study_image/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(GermplasmStudyImage $germplasmStudyImage, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($germplasmStudyImage->getId()) {
            $germplasmStudyImage->setIsActive(!$germplasmStudyImage->getIsActive());
        }
        $entmanager->persist($germplasmStudyImage);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $germplasmStudyImage->getIsActive()
        ], 200);
    }

    // this is to upload data in bulk using an excel file for germplasm x study
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function germplasmStudyUploadFromExcel(Request $request, GermplasmStudyImageRepository $gsImageRepo, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Query how many rows are there in the Germplasm table
            $rowsBeforeSaving = $gsImageRepo->getTotalRows();

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
                $germplasmID = $row['A'];
                $studyAbbreviation = $row['B'];
                $imageDesc = $row['C'];
                $factorOntId = $row['D'];
                $anatomicalEntOntId = $row['E'];
                $developmentaStageOntId = $row['F'];
                $filename = $row['G'];
                //dd("Germplasm ID ", $germplasmID, " Study ", $studyAbbreviation, " Image Desc ", $imageDesc, " filename ", $filename);
                // check if the file doesn't have empty columns
                if ($studyAbbreviation != null && $germplasmID != null && $filename != null) {
                    // check if the data is upload in the database
                    $gsImage = new GermplasmStudyImage();
                    try {
                        //code...
                        $existingGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmID]);
                        if (($existingGermplasm != null) && ($existingGermplasm instanceof \App\Entity\Germplasm)) {
                            $gsImage->setGermplasmID($existingGermplasm);
                        }
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the germplasm " .$germplasmID);
                    }

                    try {
                        //code...
                        $existingStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                        if (($existingStudy != null) && ($existingStudy instanceof \App\Entity\Study)) {
                            $gsImage->setStudyID($existingStudy);
                        }
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the study " .$studyAbbreviation);
                    }

                    try {
                        //code...
                        $existingFactorOnt = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $factorOntId]);
                        if (($existingFactorOnt != null) && ($existingFactorOnt instanceof \App\Entity\FactorType)) {
                            $gsImage->setFactor($existingFactorOnt);
                        }
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the factor type " .$factorOntId);
                    }

                    try {
                        //code...
                        $existingAnatomicalEnt = $entmanager->getRepository(AnatomicalEntity::class)->findOneBy(['ontology_id' => $anatomicalEntOntId]);
                        if (($existingAnatomicalEnt != null) && ($existingAnatomicalEnt instanceof \App\Entity\AnatomicalEntity)) {
                            $gsImage->setPlantAnatomicalEntity($existingAnatomicalEnt);
                        }
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the plant anatomical entity " .$anatomicalEntOntId);
                    }

                    try {
                        //code...
                        $existingDevelopmentalStage = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['ontology_id' => $developmentaStageOntId]);
                        if (($existingDevelopmentalStage != null) && ($existingDevelopmentalStage instanceof \App\Entity\DevelopmentalStage)) {
                            $gsImage->setDevelopmentStage($existingDevelopmentalStage);
                        }
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the developmental stage " .$developmentaStageOntId);
                    }

                    try {
                        //code...
                        $gsImage->setDescription($imageDesc);
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the image description " .$imageDesc);
                    }

                    try {
                        //code...
                        $gsImage->setFilename($filename);
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the filename " .$filename);
                    }

                    try {
                        $gsImage->setIsActive(true);
                        $gsImage->setCreatedAt(new \DateTime());
                        $entmanager->persist($gsImage);
                        $entmanager->flush();
                    } catch (\Exception $th) {
                        //throw $th;
                        $this->addFlash('danger', " Can not save your data due to " .$th->getMessage());
                    }
                }
            }
            
            // Query how many rows are there in the table
            // Query how many rows are there in the Germplasm table
            $rowsAfterSaving = $gsImageRepo->getTotalRows();

            if ($rowsBeforeSaving == 0) {
                $this->addFlash('success', "Germplams x Study Image: " .$rowsAfterSaving . " rows have been successfuly affected.");
            } else {
                $diffBeforeAndAfter = $rowsAfterSaving - $rowsBeforeSaving;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "Germplams x Study Image: No new rows have been added / affected");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', "Germplams x Study Image: " .$diffBeforeAndAfter . " row has been successfuly affected");
                } else {
                    $this->addFlash('success', "Germplams x Study Image: " .$diffBeforeAndAfter . " rows have been successfuly affected");
                }
            }
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm x Study Image Upload From Excel',
            'germplasmStudyImageUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('germplasm_study_image/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function germplasmStudyExcelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/germplasm_study_image_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'germplasm_study_image_template_example.xlsx');
        return $response;
       
    }
}
