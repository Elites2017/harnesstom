<?php

namespace App\Controller;

use App\Entity\GWAS;
use App\Entity\Study;
use App\Entity\GWASStatTest;
use App\Entity\StructureMethod;
use App\Entity\AllelicEffectEstimator;
use App\Entity\GWASModel;
use App\Entity\VariantSetMetadata;
use App\Entity\Software;
use App\Entity\GeneticTestingModel;
use App\Entity\ThresholdMethod;
use App\Entity\KinshipAlgorithm;
use App\Form\GWASType;
use App\Form\GWASUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GWASRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// set a class level route
/**
 * @Route("/gwas", name="gwas_")
 */
class GwasController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASRepository $gwasRepo): Response
    {
        $gwases =  $gwasRepo->findAll();
        // if($this->getUser()) {
        //     $userRoles = $this->getUser()->getRoles();
        //     $adm = "ROLE_ADMIN";
        //     $res = array_search($adm, $userRoles);
        //     if ($res !== false) {
        //         $gwases = $qtlStudyRepo->findAll();
        //     } else {
        //         $gwases = $qtlStudyRepo->findReleasedTrialStudyGWAS($this->getUser());
        //     }
        // } else {
        //     $gwases = $qtlStudyRepo->findReleasedTrialStudyGWAS();
        // }
        $context = [
            'title' => 'GWAS List',
            'gwases' => $gwases
        ];
        return $this->render('gwas/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwas = new GWAS();
        $form = $this->createForm(GWASType::class, $gwas);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwas->setCreatedBy($this->getUser());
            }
            $gwas->setIsActive(true);
            $gwas->setCreatedAt(new \DateTime());
            $entmanager->persist($gwas);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_index'));
        }

        $context = [
            'title' => 'GWAS Creation',
            'gwasForm' => $form->createView()
        ];
        return $this->render('gwas/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWAS $gwasSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Details',
            'gwas' => $gwasSelected
        ];
        return $this->render('gwas/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWAS $gwas, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_edit', $gwas);
        $form = $this->createForm(GWASUpdateType::class, $gwas);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwas);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_index'));
        }

        $context = [
            'title' => 'GWAS Update',
            'gwasForm' => $form->createView()
        ];
        return $this->render('gwas/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWAS $gwas, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwas->getId()) {
            $gwas->setIsActive(!$gwas->getIsActive());
        }
        $entmanager->persist($gwas);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwas->getIsActive()
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
            $repoGWAS = $entmanager->getRepository(GWAS::class);
            // Query how many rows are there in the GWAS table
            $totalGWASBefore = $repoGWAS->createQueryBuilder('tab')
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
                $gwasName = $row['A'];
                $studyIds = $row['B'];
                $variantSetMetadataName = $row['C'];
                $preProcessing = $row['D'];
                $varAssSoftware = $row['E'];
                $model = $row['F'];
                $kinshipAlgo = $row['G'];
                $structureMethod = $row['H'];
                $geneticTestModel = $row['I'];
                $allelicEffectEst = $row['J'];
                $statTest = $row['K'];
                $thresholdMethodId = $row['L'];
                $thresholdValue = $row['M'];
                $publicationRef = $row['N'];
                // check if the file doesn't have empty columns
                if ($gwasName != null && $studyIds != null && $variantSetMetadataName && $model != null
                    && $kinshipAlgo != null && $structureMethod != null && $thresholdMethodId != null && $thresholdValue != null) {
                    // check if the data is upload in the database
                    $existingGWAS = $entmanager->getRepository(GWAS::class)->findOneBy(['name' => $gwasName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingGWAS) {
                        $gwas = new GWAS();
                        if ($this->getUser()) {
                            $gwas->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $gwas->setName($gwasName);
                            
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwas name " .$gwasName);
                        }

                        try {
                            //code...
                            $gwasVariantSetMD = $entmanager->getRepository(VariantSetMetadata::class)->findOneBy(['name' => $variantSetMetadataName]);
                            if (($gwasVariantSetMD != null) && ($gwasVariantSetMD instanceof \App\Entity\VariantSetMetadata)) {
                                $gwas->setVariantSetMetadata($gwasVariantSetMD);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata name " .$variantSetMetadataName);
                        }
                        
                        try {
                            //code...
                            if ($preProcessing) {
                                $gwas->setPreprocessing($preProcessing);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwas preprocessing " .$preProcessing);
                        }
                        
                        try {
                            //code...
                            $gwasVarAssSoft = $entmanager->getRepository(Software::class)->findOneBy(['ontology_id' => $varAssSoftware]);
                            if (($gwasVarAssSoft != null) && ($gwasVarAssSoft instanceof \App\Entity\Software)) {
                                $gwas->setSoftware($gwasVarAssSoft);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the var association software " .$varAssSoftware);
                        }


                        try {
                            //code...
                            $gwasModel = $entmanager->getRepository(GWASModel::class)->findOneBy(['ontology_id' => $model]);
                            if (($gwasModel != null) && ($gwasModel instanceof \App\Entity\GWASModel)) {
                                $gwas->setGwasModel($gwasModel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwas model " .$model);
                        }

                        try {
                            //code...
                            $gwasKinshipAlgo = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['ontology_id' => $kinshipAlgo]);
                            if (($gwasKinshipAlgo != null) && ($gwasKinshipAlgo instanceof \App\Entity\KinshipAlgorithm)) {
                                $gwas->setKinshipAlgorithm($gwasKinshipAlgo);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the kinship algorithm " .$kinshipAlgo);
                        }
                        
                        try {
                            //code...
                            $gwasStructureMethod = $entmanager->getRepository(StructureMethod::class)->findOneBy(['ontology_id' => $structureMethod]);
                            if (($gwasStructureMethod != null) && ($gwasStructureMethod instanceof \App\Entity\StructureMethod)) {
                                $gwas->setStructureMethod($gwasStructureMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the structure method " .$structureMethod);
                        }

                        try {
                            //code...
                            $gwasGeneticTestModel = $entmanager->getRepository(GeneticTestingModel::class)->findOneBy(['ontology_id' => $geneticTestModel]);
                            if (($gwasGeneticTestModel != null) && ($gwasGeneticTestModel instanceof \App\Entity\GeneticTestingModel)) {
                                $gwas->setGeneticTestingModel($gwasGeneticTestModel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the genetic testing model " .$geneticTestModel);
                        }

                        try {
                            //code...
                            $gwasAllelicEE = $entmanager->getRepository(AllelicEffectEstimator::class)->findOneBy(['ontology_id' => $allelicEffectEst]);
                            if (($gwasAllelicEE != null) && ($gwasAllelicEE instanceof \App\Entity\AllelicEffectEstimator)) {
                                $gwas->setAllelicEffectEstimator($gwasAllelicEE);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the allelic effect estimator " .$allelicEffectEst);
                        }

                        try {
                            //code...
                            $gwasStatTest = $entmanager->getRepository(GWASStatTest::class)->findOneBy(['ontology_id' => $statTest]);
                            if (($gwasStatTest != null) && ($gwasStatTest instanceof \App\Entity\GWASStatTest)) {
                                $gwas->setGwasStatTest($gwasStatTest);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwas stat test " .$statTest);
                        }

                        try {
                            //code...
                            $gwasThresholdMethod = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['ontology_id' => $thresholdMethodId]);
                            if (($gwasThresholdMethod != null) && ($gwasThresholdMethod instanceof \App\Entity\ThresholdMethod)) {
                                $gwas->setThresholdMethod($gwasThresholdMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold method " .$thresholdMethodId);
                        }

                        try {
                            //code...
                            if ($thresholdValue) {
                                $gwas->setThresholdValue($thresholdValue);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold value " .$thresholdValue);
                        }

                        $publicationRef = explode(";", $publicationRef);
                        
                        try {
                            //code...
                            $gwas->setPublicationReference($publicationRef);
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
                                $gwasOneStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $oneStudy]);
                                if (($gwasOneStudy != null) && ($gwasOneStudy instanceof \App\Entity\Study)) {
                                    $gwas->addStudyList($gwasOneStudy);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the study ", $oneStudy);
                            }
                        }

                        $gwas->setIsActive(true);
                        $gwas->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($gwas);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                } else {
                    $this->addFlash('danger', " The gwas name, the study list and the variant set name can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalGWASAfter = $repoGWAS->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGWASBefore == 0) {
                $this->addFlash('success', $totalGWASAfter . " gwas have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGWASAfter - $totalGWASBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new gwas has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('gwas_index'));
        }

        $context = [
            'title' => 'GWAS Upload From Excel',
            'gwasUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('gwas/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/gwas_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'gwas_template_example.xlsx');
        return $response;
       
    }
}

