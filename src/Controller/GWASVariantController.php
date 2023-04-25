<?php

namespace App\Controller;

use App\Entity\GWASVariant;
use App\Entity\Marker;
use App\Entity\GWAS;
use App\Entity\Metabolite;
use App\Entity\TraitPreprocessing;
use App\Entity\ObservationVariable;
use App\Form\GWASVariantType;
use App\Form\GWASVariantUpdateType;
use App\Repository\GWASVariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UploadFromExcelType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// set a class level route
/**
 * @Route("/gwas/variant", name="gwas_variant_")
 */
class GWASVariantController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASVariantRepository $gwasVariantRepo): Response
    {
        $gwasVariants =  $gwasVariantRepo->findAll();
        $context = [
            'title' => 'GWAS Variant List',
            'gwasVariants' => $gwasVariants
        ];
        return $this->render('gwas_variant/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasVariant = new GWASVariant();
        $form = $this->createForm(GWASVariantType::class, $gwasVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwasVariant->setCreatedBy($this->getUser());
            }
            $gwasVariant->setIsActive(true);
            $gwasVariant->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_variant_index'));
        }

        $context = [
            'title' => 'GWAS Variant Creation',
            'gwasVariantForm' => $form->createView()
        ];
        return $this->render('gwas_variant/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASVariant $gwasVariantSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Variant Details',
            'gwasVariant' => $gwasVariantSelected
        ];
        return $this->render('gwas_variant/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASVariant $gwasVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_variant_edit', $gwasVariant);
        $form = $this->createForm(GWASVariantUpdateType::class, $gwasVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_variant_index'));
        }

        $context = [
            'title' => 'GWAS Variant Update',
            'gwasVariantForm' => $form->createView()
        ];
        return $this->render('gwas_variant/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASVariant $gwasVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasVariant->getId()) {
            $gwasVariant->setIsActive(!$gwasVariant->getIsActive());
        }
        $entmanager->persist($gwasVariant);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasVariant->getIsActive()
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
            $repoGWASVariant = $entmanager->getRepository(GWASVariant::class);
            // Query how many rows are there in the GWAS table
            $totalGWASBefore = $repoGWASVariant->createQueryBuilder('tab')
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
                $gwasVariantName = $row['B'];
                $observationVarId = $row['C'];
                $observationVarName = $row['D'];
                $metaboliteCode = $row['E'];
                $traitPreproId = $row['F'];
                $markerName = $row['G'];
                $refAllele = $row['H'];
                $altAllele = $row['I'];
                $maf = $row['J'];
                $sampleSize = $row['K'];
                $snppValue = $row['L'];
                $ajustedPVal = $row['M'];

                $allelicEffect = $row['M'];
                $allelicEffectStat = $row['M'];
                $allelicEffectdf = $row['M'];
                $allelicEffectStdE = $row['M'];
                $beta = $row['M'];
                $betaStdError = $row['M'];
                $oddsRatio = $row['M'];
                $ciLower = $row['M'];
                $ciUpper = $row['M'];
                $rSquareModelWithOutSNP = $row['M'];
                $rSquareModelSNP = $row['M'];

                // check if the file doesn't have empty columns
                if ($gwasVariantName != null && $studyIds != null && $variantSetMetadataName && $model != null
                    && $kinshipAlgo != null && $structureMethod != null && $thresholdMethodId != null && $thresholdValue != null) {
                    // check if the data is upload in the database
                    $existingGWASVariant = $entmanager->getRepository(GWASVariant::class)->findOneBy(['name' => $gwasVariantName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingGWASVariant) {
                        $gwasVariant = new GWASVariant();
                        if ($this->getUser()) {
                            $gwasVariant->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $gwasVariant->setName($gwasVariantName);
                            
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwasVariant name " .$gwasVariantName);
                        }

                        try {
                            //code...
                            $gwasVariantVariantSetMD = $entmanager->getRepository(VariantSetMetadata::class)->findOneBy(['name' => $variantSetMetadataName]);
                            if (($gwasVariantVariantSetMD != null) && ($gwasVariantVariantSetMD instanceof \App\Entity\VariantSetMetadata)) {
                                $gwasVariant->setVariantSetMetadata($gwasVariantVariantSetMD);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variant set metadata name " .$variantSetMetadataName);
                        }
                        
                        try {
                            //code...
                            if ($preProcessing) {
                                $gwasVariant->setPreprocessing($preProcessing);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwasVariant preprocessing " .$preProcessing);
                        }
                        
                        try {
                            //code...
                            $gwasVariantVarAssSoft = $entmanager->getRepository(Software::class)->findOneBy(['ontology_id' => $varAssSoftware]);
                            if (($gwasVariantVarAssSoft != null) && ($gwasVariantVarAssSoft instanceof \App\Entity\Software)) {
                                $gwasVariant->setSoftware($gwasVariantVarAssSoft);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the var association software " .$varAssSoftware);
                        }


                        try {
                            //code...
                            $gwasVariantModel = $entmanager->getRepository(GWASVariantModel::class)->findOneBy(['ontology_id' => $model]);
                            if (($gwasVariantModel != null) && ($gwasVariantModel instanceof \App\Entity\GWASVariantModel)) {
                                $gwasVariant->setGwasVariantModel($gwasVariantModel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwasVariant model " .$model);
                        }

                        try {
                            //code...
                            $gwasVariantKinshipAlgo = $entmanager->getRepository(KinshipAlgorithm::class)->findOneBy(['ontology_id' => $kinshipAlgo]);
                            if (($gwasVariantKinshipAlgo != null) && ($gwasVariantKinshipAlgo instanceof \App\Entity\KinshipAlgorithm)) {
                                $gwasVariant->setKinshipAlgorithm($gwasVariantKinshipAlgo);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the kinship algorithm " .$kinshipAlgo);
                        }
                        
                        try {
                            //code...
                            $gwasVariantStructureMethod = $entmanager->getRepository(StructureMethod::class)->findOneBy(['ontology_id' => $structureMethod]);
                            if (($gwasVariantStructureMethod != null) && ($gwasVariantStructureMethod instanceof \App\Entity\StructureMethod)) {
                                $gwasVariant->setStructureMethod($gwasVariantStructureMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the structure method " .$structureMethod);
                        }

                        try {
                            //code...
                            $gwasVariantGeneticTestModel = $entmanager->getRepository(GeneticTestingModel::class)->findOneBy(['ontology_id' => $geneticTestModel]);
                            if (($gwasVariantGeneticTestModel != null) && ($gwasVariantGeneticTestModel instanceof \App\Entity\GeneticTestingModel)) {
                                $gwasVariant->setGeneticTestingModel($gwasVariantGeneticTestModel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the genetic testing model " .$geneticTestModel);
                        }

                        try {
                            //code...
                            $gwasVariantAllelicEE = $entmanager->getRepository(AllelicEffectEstimator::class)->findOneBy(['ontology_id' => $allelicEffectEst]);
                            if (($gwasVariantAllelicEE != null) && ($gwasVariantAllelicEE instanceof \App\Entity\AllelicEffectEstimator)) {
                                $gwasVariant->setAllelicEffectEstimator($gwasVariantAllelicEE);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the allelic effect estimator " .$allelicEffectEst);
                        }

                        try {
                            //code...
                            $gwasVariantStatTest = $entmanager->getRepository(GWASVariantStatTest::class)->findOneBy(['ontology_id' => $statTest]);
                            if (($gwasVariantStatTest != null) && ($gwasVariantStatTest instanceof \App\Entity\GWASVariantStatTest)) {
                                $gwasVariant->setGwasVariantStatTest($gwasVariantStatTest);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwasVariant stat test " .$statTest);
                        }

                        try {
                            //code...
                            $gwasVariantThresholdMethod = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['ontology_id' => $thresholdMethodId]);
                            if (($gwasVariantThresholdMethod != null) && ($gwasVariantThresholdMethod instanceof \App\Entity\ThresholdMethod)) {
                                $gwasVariant->setThresholdMethod($gwasVariantThresholdMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold method " .$thresholdMethodId);
                        }

                        try {
                            //code...
                            if ($thresholdValue) {
                                $gwasVariant->setThresholdValue($thresholdValue);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the threshold value " .$thresholdValue);
                        }

                        $publicationRef = explode(";", $publicationRef);
                        
                        try {
                            //code...
                            $gwasVariant->setPublicationReference($publicationRef);
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
                                $gwasVariantOneStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $oneStudy]);
                                if (($gwasVariantOneStudy != null) && ($gwasVariantOneStudy instanceof \App\Entity\Study)) {
                                    $gwasVariant->addStudyList($gwasVariantOneStudy);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the study ", $oneStudy);
                            }
                        }

                        $gwasVariant->setIsActive(true);
                        $gwasVariant->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($gwasVariant);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                } else {
                    $this->addFlash('danger', " The gwasVariant name, the study list and the variant set name can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalGWASVariantAfter = $repoGWASVariant->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGWASVariantBefore == 0) {
                $this->addFlash('success', $totalGWASVariantAfter . " gwasVariant have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGWASVariantAfter - $totalGWASVariantBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new gwasVariant has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwasVariant has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwasVariant have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('gwas_variant_index'));
        }

        $context = [
            'title' => 'GWAS Variant Upload From Excel',
            'gwasVariantUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('gwas_variant/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/gwas_variant_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'gwas_variant_template_example.xlsx');
        return $response;
       
    }
}

