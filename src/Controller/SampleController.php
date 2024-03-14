<?php

namespace App\Controller;

use App\Entity\AnatomicalEntity;
use App\Entity\DevelopmentalStage;
use App\Entity\Germplasm;
use App\Entity\ObservationLevel;
use App\Entity\Sample;
use App\Entity\Study;
use App\Form\SampleType;
use App\Form\SampleUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SampleRepository;
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
 * @Route("/sample", name="sample_")
 */
class SampleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SampleRepository $sampleRepo): Response
    {
        $samples = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $samples = $sampleRepo->findAll();
            } else {
                $samples = $sampleRepo->findReleasedTrialStudySample($this->getUser());
            }
        } else {
            $samples = $sampleRepo->findReleasedTrialStudySample();
        }
        $context = [
            'title' => 'Sample List',
            'samples' => $samples
        ];
        return $this->render('sample/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sample = new Sample();
        $form = $this->createForm(SampleType::class, $sample);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $sample->setCreatedBy($this->getUser());
            }
            $sample->setIsActive(true);
            $sample->setCreatedAt(new \DateTime());
            $entmanager->persist($sample);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('sample_index'));
        }

        $context = [
            'title' => 'Sample Creation',
            'sampleForm' => $form->createView()
        ];
        return $this->render('sample/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(sample $sampleSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sample Details',
            'sample' => $sampleSelected
        ];
        return $this->render('sample/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(sample $sample, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('sample_edit', $sample);
        $form = $this->createForm(SampleUpdateType::class, $sample);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sample->setLastUpdated(new \DateTime());
            $entmanager->persist($sample);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('sample_index'));
        }


        $context = [
            'title' => 'Sample Update',
            'sampleForm' => $form->createView()
        ];
        return $this->render('sample/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(sample $sample, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sample->getId()) {
            $sample->setIsActive(!$sample->getIsActive());
        }
        $entmanager->persist($sample);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sample->getIsActive()
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
            $repoSample = $entmanager->getRepository(Sample::class);
            // Query how many rows are there in the Sample table
            $totalSampleBefore = $repoSample->createQueryBuilder('tab')
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
                $observationLevelUnitName = $row['C'];
                $replicate = $row['D'];
                $sampleName = $row['E'];
                $anatomicalEntOntId = $row['F'];
                $developmentStageOntId = $row['G'];
                $sampleDescription = $row['H'];
                // check if the file doesn't have empty columns
                if ($sampleName != null && $studyAbbreviation != null) {
                    // check if the data is upload in the database
                    $existingSample = $entmanager->getRepository(Sample::class)->findOneBy(['name' => $sampleName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingSample) {
                        $sample = new Sample();
                        if ($this->getUser()) {
                            $sample->setCreatedBy($this->getUser());
                        }
                        
                        try {
                            //code...
                            $sampleStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                            if (($sampleStudy != null) && ($sampleStudy instanceof \App\Entity\Study)) {
                                $sample->setStudy($sampleStudy);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study abbreaviation " .$studyAbbreviation);
                        }

                        try {
                            //code...
                            $sampleGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmID]);
                            if (($sampleGermplasm != null) && ($sampleGermplasm instanceof \App\Entity\Germplasm)) {
                                $sample->setGermplasm($sampleGermplasm);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the germplasm ID " .$germplasmID);
                        }

                        try {
                            //code...
                            $sampleObservationLevel = $entmanager->getRepository(ObservationLevel::class)->findOneBy(['unitname' => $observationLevelUnitName]);
                            if (($sampleObservationLevel != null) && ($sampleObservationLevel instanceof \App\Entity\ObservationLevel)) {
                                $sample->setObservationLevel($sampleObservationLevel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation level unit name " .$observationLevelUnitName);
                        }

                        try {
                            //code...
                            $sampleAnatomicalEntity = $entmanager->getRepository(AnatomicalEntity::class)->findOneBy(['ontology_id' => $anatomicalEntOntId]);
                            if (($sampleAnatomicalEntity != null) && ($sampleAnatomicalEntity instanceof \App\Entity\AnatomicalEntity)) {
                                $sample->setAnatomicalEntity($sampleAnatomicalEntity);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the anatomical entity ontology ID " .$anatomicalEntOntId);
                        }

                        try {
                            //code...
                            $sampleDevelopmentalStage = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['ontology_id' => $developmentStageOntId]);
                            if (($sampleDevelopmentalStage != null) && ($sampleDevelopmentalStage instanceof \App\Entity\DevelopmentalStage)) {
                                $sample->setDevelopmentalStage($sampleDevelopmentalStage);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the developmental stage ontology ID " .$developmentStageOntId);
                        }

                        try {
                            //code...
                            $sample->setName($sampleName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the sample name " .$sampleName);
                        }

                        try {
                            //code...
                            if ($replicate) {
                                $sample->setReplicate($replicate);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the replicate " .$replicate);
                        }

                        try {
                            //code...
                            if ($sampleDescription) {
                                $sample->setDescription($sampleDescription);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the sample description " .$sampleDescription);
                        }

                        $sample->setIsActive(true);
                        $sample->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($sample);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalSampleAfter = $repoSample->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSampleBefore == 0) {
                $this->addFlash('success', $totalSampleAfter . " samples have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSampleAfter - $totalSampleBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new sample has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " sample has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " samples have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('sample_index'));
        }

        $context = [
            'title' => 'Sample Upload From Excel',
            'sampleUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('sample/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/sample_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'sample_template_example.xlsx');
        return $response;
       
    }
}
