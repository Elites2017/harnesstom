<?php

namespace App\Controller;

use App\Entity\Germplasm;
use App\Entity\Marker;
use App\Entity\Metabolite;
use App\Entity\ObservationVariable;
use App\Entity\QTLStudy;
use App\Entity\QTLVariant;
use App\Entity\Study;
use App\Form\QTLVariantType;
use App\Form\QTLVariantUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\QTLVariantRepository;
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
            if (($qtlVariant->getObservationVariable() == null) && ($qtlVariant->getMetabolite() == null)) {
                $this->addFlash('danger', "You need to specify the type of phenotyping data you're submitting. Observation Variable or Metabolite");
            } else {
                if ($this->getUser()) {
                    $qtlVariant->setCreatedBy($this->getUser());
                }
                $qtlVariant->setIsActive(true);
                $qtlVariant->setCreatedAt(new \DateTime());
                $entmanager->persist($qtlVariant);
                $entmanager->flush();
                $this->addFlash('success', "new qtl variant successfully added");
                return $this->redirect($this->generateUrl('qtl_variant_index'));
            }
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
            if (($qtlVariant->getObservationVariable() == null) && ($qtlVariant->getMetabolite() == null)) {
                $this->addFlash('danger', "You need to specify the type of phenotyping data you're submitting. Observation Variable or Metabolite");
            } else {
                $entmanager->persist($qtlVariant);
                $entmanager->flush();
                $this->addFlash('success', "new qtl variant successfully edited");
                return $this->redirect($this->generateUrl('qtl_variant_index'));
            }
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
            $repoQTLVariant = $entmanager->getRepository(QTLVariant::class);
            // Query how many rows are there in the GWAS table
            $totalQTLVariantBefore = $repoQTLVariant->createQueryBuilder('tab')
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
                $qtlName = $row['B'];
                $observationVarId = $row['C'];
                $observationVarName = $row['D'];
                $metaboliteCode = $row['E'];
                $qtlDetectName = $row['F'];
                $originaltraitName = $row['G'];
                $linkageGroupName = $row['H'];
                $peakPosition = $row['I'];
                $closestMarker = $row['J'];
                $ciStart = $row['K'];
                $ciEnd = $row['L'];
                $flankingMarkerStart = $row['M'];
                $flankingMarkerEnd = $row['M'];
                $positiveAlleleparent = $row['O'];
                $psotiveAllele = $row['P'];
                $qtlStatValue = $row['Q'];
                $additive = $row['R'];
                $dominance = $row['S'];
                $dA = $row['T'];
                $r2 = $row['U'];
                $statisticQTLPEV = $row['V'];
                $r2QTLxE = $row['W'];
                $r2Global = $row['X'];
                $locus = $row['Y'];
                $locusName = $row['Z'];
                $publicationRef = $row['AA'];

                // check if the file doesn't have empty columns
                if ($qtlStudyName != null && $qtlName != null && $linkageGroupName != null && $closestMarker != null
                    && $qtlStatValue != null && $r2 != null && $peakPosition != null) {
                    // check if the metabolite or observation variable is empty
                    if (($observationVarId == null) && ($metaboliteCode == null)) {
                        $this->addFlash('danger', "You need to specify the type of phenotyping data you're submitting. Observation Variable or Metabolite can not be empty");
                    } else {
                        // check if the data is upload in the database
                        $existingQTLVariant = $entmanager->getRepository(QTLVariant::class)->findOneBy(['name' => $qtlName]);
                        // upload data only for objects that haven't been saved in the database
                        if (!$existingQTLVariant) {
                            $qtlVariant = new QTLVariant();
                            if ($this->getUser()) {
                                $qtlVariant->setCreatedBy($this->getUser());
                            }

                            try {
                                //code...
                                $qtlVariant->setName($qtlName);
                                
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the qtl variant name " .$qtlName);
                            }

                            try {
                                //code...
                                $qtlVariantQTLStudy = $entmanager->getRepository(QTLStudy::class)->findOneBy(['name' => $qtlStudyName]);
                                if (($qtlVariantQTLStudy != null) && ($qtlVariantQTLStudy instanceof \App\Entity\QTLStudy)) {
                                    $qtlVariant->setQtlStudy($qtlVariantQTLStudy);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the qtl study name " .$qtlStudyName);
                            }

                            try {
                                //code...
                                $qtlVariantClosestMarker = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $closestMarker]);
                                if (($qtlVariantClosestMarker != null) && ($qtlVariantClosestMarker instanceof \App\Entity\Marker)) {
                                    $qtlVariant->setClosestMarker($qtlVariantClosestMarker);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the closest marker name " .$closestMarker);
                            }

                            try {
                                //code...
                                $qtlVariantFlankingMarkerStart = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $flankingMarkerStart]);
                                if (($qtlVariantFlankingMarkerStart != null) && ($qtlVariantFlankingMarkerStart instanceof \App\Entity\Marker)) {
                                    $qtlVariant->setClosestMarker($qtlVariantFlankingMarkerStart);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the flanking marker start name " .$flankingMarkerStart);
                            }

                            try {
                                //code...
                                $qtlVariantFlankingMarkerEnd = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $flankingMarkerEnd]);
                                if (($qtlVariantFlankingMarkerEnd != null) && ($qtlVariantFlankingMarkerEnd instanceof \App\Entity\Marker)) {
                                    $qtlVariant->setClosestMarker($qtlVariantFlankingMarkerEnd);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the flanking marker end name " .$flankingMarkerEnd);
                            }

                            try {
                                //code...
                                $qtlVariantPositiveAlleleParent = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $positiveAlleleparent]);
                                if (($qtlVariantPositiveAlleleParent != null) && ($qtlVariantPositiveAlleleParent instanceof \App\Entity\Germplasm)) {
                                    $qtlVariant->setPositiveAlleleParent($qtlVariantPositiveAlleleParent);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the positive allele parent name " .$positiveAlleleparent);
                            }

                            try {
                                //code...
                                $qtlVariantMetabolite = $entmanager->getRepository(Metabolite::class)->findOneBy(['id' => $metaboliteCode]);
                                if (($qtlVariantMetabolite != null) && ($qtlVariantMetabolite instanceof \App\Entity\Metabolite)) {
                                    $qtlVariant->setMetabolite($qtlVariantMetabolite);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the metabolite code " .$metaboliteCode);
                            }

                            try {
                                //code...
                                $qtlVariantObservationVariable = $entmanager->getRepository(ObservationVariable::class)->findOneBy(['id' => $observationVarId]);
                                if (($qtlVariantObservationVariable != null) && ($qtlVariantObservationVariable instanceof \App\Entity\ObservationVariable)) {
                                    $qtlVariant->setObservationVariable($qtlVariantObservationVariable);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the observation variable " .$observationVarId);
                            }

                            if ($qtlDetectName) {
                                try {
                                    //code...
                                    $qtlVariant->setDetectName($qtlDetectName);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the qtl detect name " .$qtlDetectName);
                                }
                            }

                            if ($originaltraitName) {
                                try {
                                    //code...
                                    $qtlVariant->setOriginalTraitName($originaltraitName);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the original trait name " .$originaltraitName);
                                }
                            }

                            if ($ciStart) {
                                try {
                                    //code...
                                    $qtlVariant->setCiStart($ciStart);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the ci start " .$ciStart);
                                }
                            }

                            if ($ciEnd) {
                                try {
                                    //code...
                                    $qtlVariant->setCiEnd($ciEnd);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the ci end " .$ciEnd);
                                }
                            }
                            
                            if ($dominance) {
                                try {
                                    //code...
                                    $qtlVariant->setDominance($dominance);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the dominance " .$dominance);
                                }
                            }

                            if ($additive) {
                                try {
                                    //code...
                                    $qtlVariant->setAdditive($additive);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the additive " .$additive);
                                }
                            }

                            if ($dA) {
                                try {
                                    //code...
                                    $qtlVariant->setDA($dA);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the dA " .$dA);
                                }
                            }

                            if ($locus) {
                                try {
                                    //code...
                                    $qtlVariant->setLocus($locus);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the locus " .$locus);
                                }
                            }

                            if ($locusName) {
                                try {
                                    //code...
                                    $qtlVariant->setLocusName($locusName);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the locus name " .$locusName);
                                }
                            }

                            if ($statisticQTLPEV) {
                                try {
                                    //code...
                                    $qtlVariant->setStatisticQTLxEValue($statisticQTLPEV);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the statistic qtl PEV " .$statisticQTLPEV);
                                }
                            }

                            if ($r2QTLxE) {
                                try {
                                    //code...
                                    $qtlVariant->setR2QTLxE($r2QTLxE);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the r2 qtl x E " .$r2QTLxE);
                                }
                            }

                            if ($r2Global) {
                                try {
                                    //code...
                                    $qtlVariant->setR2Global($r2Global);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the r2 global " .$r2Global);
                                }
                            }

                            if ($psotiveAllele) {
                                try {
                                    //code...
                                    $qtlVariant->setPositiveAllele($psotiveAllele);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the positive allele " .$psotiveAllele);
                                }
                            }

                            $publicationRef = explode(",", $publicationRef);

                            if ($publicationRef) {
                                try {
                                    //code...
                                    $qtlVariant->setPublicationReference($publicationRef);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the publication reference " .$publicationRef);
                                }
                            }

                            $qtlVariant->setIsActive(true);
                            $qtlVariant->setCreatedAt(new \DateTime());
                            try {
                                //code...
                                $entmanager->persist($qtlVariant);
                                $entmanager->flush();
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                            }
                        }
                    }
                } else {
                    $this->addFlash('danger', " The qtl variant name, the qtl study name and the closest marker name can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalQTLVariantAfter = $repoQTLVariant->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalQTLVariantBefore == 0) {
                $this->addFlash('success', $totalQTLVariantAfter . " qtl variant have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalQTLVariantAfter - $totalQTLVariantBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new qtl variant has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl variant has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl variant have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('qtl_variant_index'));
        }

        $context = [
            'title' => 'QTL Variant Upload From Excel',
            'qtlVariantUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('qtl_variant/upload_from_excel.html.twig', $context);
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

