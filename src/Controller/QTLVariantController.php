<?php

namespace App\Controller;

use App\Entity\QTLVariant;
use App\Form\QTLVariantType;
use App\Form\QTLVariantUpdateType;
use App\Repository\QTLVariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/qtl/variant", name="qtl_variant_")
 */
class QTLVariantController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLVariantRepository $qtlVariantRepo): Response
    {
        $qtlVariants =  $qtlVariantRepo->findAll();
        $context = [
            'title' => 'QTL Variant List',
            'qtlVariants' => $qtlVariants
        ];
        return $this->render('qtl_variant/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlVariant = new QTLVariant();
        $form = $this->createForm(QTLVariantType::class, $qtlVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlVariant->setCreatedBy($this->getUser());
            }
            $qtlVariant->setIsActive(true);
            $qtlVariant->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_variant_index'));
        }

        $context = [
            'title' => 'QTL Variant Creation',
            'qtlVariantForm' => $form->createView()
        ];
        return $this->render('qtl_variant/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLVariant $qtlVariantSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Variant Details',
            'qtlVariant' => $qtlVariantSelected
        ];
        return $this->render('qtl_variant/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLVariant $qtlVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_variant_edit', $qtlVariant);
        $form = $this->createForm(QTLVariantUpdateType::class, $qtlVariant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlVariant);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_variant_index'));
        }

        $context = [
            'title' => 'QTL Variant Update',
            'qtlVariantForm' => $form->createView()
        ];
        return $this->render('qtl_variant/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLVariant $qtlVariant, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlVariant->getId()) {
            $qtlVariant->setIsActive(!$qtlVariant->getIsActive());
        }
        $entmanager->persist($qtlVariant);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlVariant->getIsActive()
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
            $totalGWASVariantBefore = $repoGWASVariant->createQueryBuilder('tab')
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

                $allelicEffect = $row['N'];
                $allelicEffectStat = $row['O'];
                $allelicEffectdf = $row['P'];
                $allelicEffectStdE = $row['Q'];
                $beta = $row['R'];
                $betaStdError = $row['S'];
                $oddsRatio = $row['T'];
                $ciLower = $row['U'];
                $ciUpper = $row['V'];
                $rSquareModelWithOutSNP = $row['W'];
                $rSquareModelSNP = $row['X'];

                // check if the file doesn't have empty columns
                if ($gwasName != null && $gwasVariantName != null && $observationVarId && $markerName != null) {
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
                            $this->addFlash('danger', " there is a problem with the gwas variant name " .$gwasVariantName);
                        }

                        try {
                            //code...
                            $gwasVariantGWAS = $entmanager->getRepository(GWAS::class)->findOneBy(['name' => $gwasName]);
                            if (($gwasVariantGWAS != null) && ($gwasVariantGWAS instanceof \App\Entity\GWAS)) {
                                $gwasVariant->setGwas($gwasVariantGWAS);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the gwas name " .$gwasName);
                        }

                        try {
                            //code...
                            $gwasVariantMarker = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $markerName]);
                            if (($gwasVariantMarker != null) && ($gwasVariantMarker instanceof \App\Entity\Marker)) {
                                $gwasVariant->setMarker($gwasVariantMarker);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the marker name " .$markerName);
                        }

                        try {
                            //code...
                            $gwasVariantTraitPrepro = $entmanager->getRepository(TraitProcessing::class)->findOneBy(['ontology_id' => $traitPreproId]);
                            if (($gwasVariantTraitPrepro != null) && ($gwasVariantTraitPrepro instanceof \App\Entity\TraitProcessing)) {
                                $gwasVariant->setTraitPreprocessing($gwasVariantTraitPrepro);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the trait preprocessing id " .$traitPreproId);
                        }

                        try {
                            //code...
                            $gwasVariantMetabolite = $entmanager->getRepository(Metabolite::class)->findOneBy(['scale' => $metaboliteCode]);
                            if (($gwasVariantMetabolite != null) && ($gwasVariantMetabolite instanceof \App\Entity\Metabolite)) {
                                $gwasVariant->setMetabolite($gwasVariantMetabolite);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the metabolite code " .$metaboliteCode);
                        }
                        
                        try {
                            //code...
                            $gwasVariantObsVar = $entmanager->getRepository(ObservationVariable::class)->findOneBy(['name' => $observationVarName]);
                            if (($gwasVariantObsVar != null) && ($gwasVariantObsVar instanceof \App\Entity\ObservationVariable)) {
                                $gwasVariant->setObservationVariable($gwasVariantObsVar);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable " .$observationVarName);
                        }


                        if ($maf) {
                            try {
                                //code...
                                $gwasVariant->setMaf($maf);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the maf " .$maf);
                            }
                        }

                        if ($refAllele) {
                            try {
                                //code...
                                $gwasVariant->setRefAllele($refAllele);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the reference allele " .$refAllele);
                            }
                        }

                        if ($altAllele) {
                            try {
                                //code...
                                $gwasVariant->setAlternativeAllele($altAllele);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the alternative allele " .$altAllele);
                            }
                        }
                        
                        if ($sampleSize) {
                            try {
                                //code...
                                $gwasVariant->setSampleSize($sampleSize);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the sample size " .$sampleSize);
                            }
                        }

                        if ($snppValue) {
                            try {
                                //code...
                                $gwasVariant->setSnppValue($snppValue);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the snpp value " .$snppValue);
                            }
                        }

                        if ($ajustedPVal) {
                            try {
                                //code...
                                $gwasVariant->setAdjustedPValue($ajustedPVal);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the ajusted pvalue " .$ajustedPVal);
                            }
                        }

                        if ($allelicEffect) {
                            try {
                                //code...
                                $gwasVariant->setAllelicEffect($allelicEffect);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the allelic effect " .$allelicEffect);
                            }
                        }

                        if ($allelicEffectStat) {
                            try {
                                //code...
                                $gwasVariant->setAllelicEffectStat($allelicEffectStat);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the allelic effect stat " .$allelicEffectStat);
                            }
                        }

                        if ($allelicEffectdf) {
                            try {
                                //code...
                                $gwasVariant->setAllelicEffectdf($allelicEffectdf);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the allelic effect df " .$allelicEffectdf);
                            }
                        }

                        if ($allelicEffectStdE) {
                            try {
                                //code...
                                $gwasVariant->setAllelicEffStdE($allelicEffectStdE);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the allelic effect stdE " .$allelicEffectStdE);
                            }
                        }

                        if ($beta) {
                            try {
                                //code...
                                $gwasVariant->setBeta($beta);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the beta " .$beta);
                            }
                        }

                        if ($betaStdError) {
                            try {
                                //code...
                                $gwasVariant->setBetaStdE($betaStdError);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the beta stdError " .$betaStdError);
                            }
                        }

                        if ($oddsRatio) {
                            try {
                                //code...
                                $gwasVariant->setOddsRatio($oddsRatio);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the odds ratio " .$oddsRatio);
                            }
                        }

                        if ($ciLower) {
                            try {
                                //code...
                                $gwasVariant->setCiLower($ciLower);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the ci lower " .$ciLower);
                            }
                        }

                        if ($ciUpper) {
                            try {
                                //code...
                                $gwasVariant->setCiUpper($ciUpper);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the ci upper " .$ciUpper);
                            }
                        }

                        if ($rSquareModelSNP) {
                            try {
                                //code...
                                $gwasVariant->setRSquareOfModeWithSNP($rSquareModelSNP);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the rsquare of mode with snp " .$rSquareModelSNP);
                            }
                        }

                        if ($rSquareModelWithOutSNP) {
                            try {
                                //code...
                                $gwasVariant->setRSquareOfModeWithoutSNP($rSquareModelWithOutSNP);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the rsquare of mode without snp " .$rSquareModelWithOutSNP);
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
                    $this->addFlash('danger', " The gwas variant name, the gwas name and the marker name can not be empty, provide them and try again");
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
                $this->addFlash('success', $totalGWASVariantAfter . " gwas variant have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGWASVariantAfter - $totalGWASVariantBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new gwas variant has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas variant has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas variant have been successfuly added");
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
        $response = new BinaryFileResponse('../public/todownload/qtl_variant_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qtl_variant_template_example.xlsx');
        return $response;
       
    }
}

