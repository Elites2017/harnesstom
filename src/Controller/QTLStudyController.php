<?php

namespace App\Controller;

use App\Entity\QTLStudy;
use App\Entity\Software;
use App\Entity\VariantSetMetadata;
use App\Entity\Unit;
use App\Entity\MappingPopulation;
use App\Entity\CiCriteria;
use App\Entity\QTLMethod;
use App\Entity\QTLStatistic;
use App\Entity\ThresholdMethod;
use App\Entity\Study;
use App\Form\QTLStudyType;
use App\Form\QTLStudyUpdateType;
use App\Repository\QTLStudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Form\UploadFromExcelType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// set a class level route
/**
 * @Route("/qtl/study", name="qtl_study_")
 */
class QTLStudyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLStudyRepository $qtlStudyRepo): Response
    {
        $qtlStudies =  $qtlStudyRepo->findAll();
        $context = [
            'title' => 'QTL Study',
            'qtlStudies' => $qtlStudies
        ];
        return $this->render('qtl_study/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlStudy = new QTLStudy();
        $form = $this->createForm(QTLStudyType::class, $qtlStudy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlStudy->setCreatedBy($this->getUser());
            }
            $qtlStudy->setIsActive(true);
            $qtlStudy->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlStudy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_study_index'));
        }

        $context = [
            'title' => 'QTL Study Creation',
            'qtlStudyForm' => $form->createView()
        ];
        return $this->render('qtl_study/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLStudy $qtlStudySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Study Details',
            'qtlStudy' => $qtlStudySelected
        ];
        return $this->render('qtl_study/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLStudy $qtlStudy, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_study_edit', $qtlStudy);
        $form = $this->createForm(QTLStudyUpdateType::class, $qtlStudy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlStudy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_study_index'));
        }

        $context = [
            'title' => 'QTL Study Update',
            'qtlStudyForm' => $form->createView()
        ];
        return $this->render('qtl_study/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLStudy $qtlStudy, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlStudy->getId()) {
            $qtlStudy->setIsActive(!$qtlStudy->getIsActive());
        }
        $entmanager->persist($qtlStudy);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlStudy->getIsActive()
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
            $repoQTLStudy = $entmanager->getRepository(QTLStudy::class);
            // Query how many rows are there in the QTLStudy table
            $totalQTLStudyBefore = $repoQTLStudy->createQueryBuilder('tab')
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
                $qtlStudyName = $row['A'];
                $studyIds = $row['B'];
                $variantSetMetadataName = $row['C'];
                $mappingPopName = $row['D'];
                $genomeMapUnit = $row['E'];
                $varAssSoftware = $row['F'];
                $qtlMethod = $row['G'];
                $qtlStatistic = $row['H'];
                $multiEnvStatId = $row['I'];
                $epistatisticId = $row['J'];
                $ciCriteria = $row['K'];
                $thresholdMethodId = $row['L'];
                $thresholdValue = $row['M'];
                $qtlCount = $row['N'];
                $publicationRef = $row['O'];
                // check if the file doesn't have empty columns
                if ($qtlStudyName != null && $studyIds != null && $variantSetMetadataName && $mappingPopName != null
                    && $genomeMapUnit != null && $qtlStatistic != null && $thresholdMethodId != null && $thresholdValue != null) {
                    // check if the data is upload in the database
                    $existingQTLStudy = $entmanager->getRepository(QTLStudy::class)->findOneBy(['name' => $qtlStudyName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingQTLStudy) {
                        $qtlStudy = new QTLStudy();
                        if ($this->getUser()) {
                            $qtlStudy->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $qtlStudy->setName($qtlStudyName);
                            
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the qtl study name " .$qtlStudyName);
                        }

                        try {
                            //code...
                            $qtlStudyVariantSetMD = $entmanager->getRepository(VariantSetMetadata::class)->findOneBy(['name' => $variantSetMetadataName]);
                            if (($qtlStudyVariantSetMD != null) && ($qtlStudyVariantSetMD instanceof \App\Entity\VariantSetMetadata)) {
                                $qtlStudy->setVariantSetMetadata($qtlStudyVariantSetMD);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata name " .$variantSetMetadataName);
                        }
                        
                        try {
                            //code...
                            $qtlMappingPop = $entmanager->getRepository(MappingPopulation::class)->findOneBy(['name' => $mappingPopName]);
                            if (($qtlMappingPop != null) && ($qtlMappingPop instanceof \App\Entity\MappingPopulation)) {
                                $qtlStudy->setMappingPopulation($qtlMappingPop);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the mapping population name " .$mappingPopName);
                        }

                        try {
                            //code...
                            $qtlStudyGenomeMapUnit = $entmanager->getRepository(Unit::class)->findOneBy(['ontology_id' => $genomeMapUnit]);
                            if (($qtlStudyGenomeMapUnit != null) && ($qtlStudyGenomeMapUnit instanceof \App\Entity\Unit)) {
                                $qtlStudy->setGenomeMapUnit($qtlStudyGenomeMapUnit);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the genome map unit " .$genomeMapUnit);
                        }
                        
                        try {
                            //code...
                            $qtlStudyVarAssSoft = $entmanager->getRepository(Software::class)->findOneBy(['ontology_id' => $varAssSoftware]);
                            if (($qtlStudyVarAssSoft != null) && ($qtlStudyVarAssSoft instanceof \App\Entity\Software)) {
                                $qtlStudy->setSoftware($qtlStudyVarAssSoft);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the var association software " .$varAssSoftware);
                        }

                        try {
                            //code...
                            $qtlStudyQTLMethod = $entmanager->getRepository(QTLMethod::class)->findOneBy(['ontology_id' => $qtlMethod]);
                            if (($qtlStudyQTLMethod != null) && ($qtlStudyQTLMethod instanceof \App\Entity\QTLMethod)) {
                                $qtlStudy->setMethod($qtlStudyQTLMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the qtl method " .$qtlMethod);
                        }
                        
                        try {
                            //code...
                            $qtlStudyQTLStatistic = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['ontology_id' => $qtlStatistic]);
                            if (($qtlStudyQTLStatistic != null) && ($qtlStudyQTLStatistic instanceof \App\Entity\QTLStatistic)) {
                                $qtlStudy->setStatistic($qtlStudyQTLStatistic);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the qtl statistic " .$qtlStatistic);
                        }

                        try {
                            //code...
                            $qtlStudyMultiEnvStat = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['ontology_id' => $multiEnvStatId]);
                            if (($qtlStudyMultiEnvStat != null) && ($qtlStudyMultiEnvStat instanceof \App\Entity\QTLStatistic)) {
                                $qtlStudy->setMultiEnvironmentStat($qtlStudyMultiEnvStat);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the multi env statistic " .$multiEnvStatId);
                        }

                        try {
                            //code...
                            $qtlStudyEpiStatistic = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['ontology_id' => $epistatisticId]);
                            if (($qtlStudyEpiStatistic != null) && ($qtlStudyEpiStatistic instanceof \App\Entity\QTLStatistic)) {
                                $qtlStudy->setEpistasisStatistic($qtlStudyEpiStatistic);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the epi statistic id " .$epistatisticId);
                        }

                        try {
                            //code...
                            $qtlStudyCiCriteria = $entmanager->getRepository(CiCriteria::class)->findOneBy(['ontology_id' => $ciCriteria]);
                            if (($qtlStudyCiCriteria != null) && ($qtlStudyCiCriteria instanceof \App\Entity\CiCriteria)) {
                                $qtlStudy->setCiCriteria($qtlStudyCiCriteria);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the ci criteria " .$ciCriteria);
                        }

                        try {
                            //code...
                            $qtlStudyThresholdMethod = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['ontology_id' => $thresholdMethodId]);
                            if (($qtlStudyThresholdMethod != null) && ($qtlStudyThresholdMethod instanceof \App\Entity\ThresholdMethod)) {
                                $qtlStudy->setThresholdMethod($qtlStudyThresholdMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold method " .$thresholdMethodId);
                        }

                        try {
                            //code...
                            if ($thresholdValue) {
                                $qtlStudy->setThresholdValue($thresholdValue);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold value " .$thresholdValue);
                        }

                        try {
                            //code...
                            if ($qtlCount) {
                                $qtlStudy->setQtlCount($qtlCount);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the qtl count " .$qtlCount);
                        }

                        $publicationRef = explode(",", $publicationRef);
                        
                        try {
                            //code...
                            $qtlStudy->setPublicationReference($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the publication reference ");
                        }
                        
                        // study list
                        $studyIds = explode(";", $studyIds);
                        foreach ($studyIds as $key => $oneStudy) {
                            # code...
                            try {
                                //code...
                                $qtlStudyOneStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $oneStudy]);
                                if (($qtlStudyOneStudy != null) && ($qtlStudyOneStudy instanceof \App\Entity\Study)) {
                                    $qtlStudy->addStudyList($qtlStudyOneStudy);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the study ", $oneStudy);
                            }
                        }

                        $qtlStudy->setIsActive(true);
                        $qtlStudy->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($qtlStudy);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                } else {
                    $this->addFlash('danger', " The qtl study name, The study list and the variant set name can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalQTLStudyAfter = $repoQTLStudy->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalQTLStudyBefore == 0) {
                $this->addFlash('success', $totalQTLStudyAfter . " qtl studies have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalQTLStudyAfter - $totalQTLStudyBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new qtl study has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl study has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl studies have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('qtl_study_index'));
        }

        $context = [
            'title' => 'QTL Study Upload From Excel',
            'qtlStudyUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('qtl_study/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/qtl_study_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qtl_study_template_example.xlsx');
        return $response;
       
    }
}
