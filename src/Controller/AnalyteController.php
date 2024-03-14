<?php

namespace App\Controller;

use App\Entity\Analyte;
use App\Entity\AnalyteFlavorHealth;
use App\Entity\AnnotationLevel;
use App\Entity\IdentificationLevel;
use App\Entity\MetaboliteClass;
use App\Entity\ObservationVariableMethod;
use App\Form\AnalyteType;
use App\Form\AnalyteUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AnalyteRepository;
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
 * @Route("/analyte", name="analyte_")
 */
class AnalyteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnalyteRepository $analyteRepo): Response
    {
        $analytes =  $analyteRepo->findAll();
        $context = [
            'title' => 'Analyte List',
            'analytes' => $analytes
        ];
        return $this->render('analyte/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $analyte = new Analyte();
        $form = $this->createForm(AnalyteType::class, $analyte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $analyte->setCreatedBy($this->getUser());
            }
            $analyte->setIsActive(true);
            $analyte->setCreatedAt(new \DateTime());
            $entmanager->persist($analyte);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('analyte_index'));
        }

        $context = [
            'title' => 'Analyte Creation',
            'analyteForm' => $form->createView()
        ];
        return $this->render('analyte/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Analyte $analyteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Analyte Details',
            'analyte' => $analyteSelected
        ];
        return $this->render('analyte/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Analyte $analyte, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('analyte_edit', $analyte);
        $form = $this->createForm(AnalyteUpdateType::class, $analyte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($analyte);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('analyte_index'));
        }

        $context = [
            'title' => 'Analyte Update',
            'analyteForm' => $form->createView()
        ];
        return $this->render('analyte/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Analyte $analyte, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($analyte->getId()) {
            $analyte->setIsActive(!$analyte->getIsActive());
        }
        $entmanager->persist($analyte);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $analyte->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('season_home'));
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
            $repoAnalyte = $entmanager->getRepository(Analyte::class);
            // Query how many rows are there in the Analyte table
            $totalAnalyteBefore = $repoAnalyte->createQueryBuilder('tab')
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
                $analyteCode = $row['A'];
                $retentiontTime = $row['B'];
                $massToChargeRatio = $row['C'];
                $name = $row['D'];
                $annotationLevelOntId = $row['E'];
                $identificationLevelOntId = $row['F'];
                $obsVariableMethodName = $row['G'];
                $metaboliteClassOntId = $row['H'];
                $helathFlavorOntId = $row['I'];
                // check if the file doesn't have empty columns
                if ($analyteCode != null && $retentiontTime != null && $massToChargeRatio != null && $name != null && $obsVariableMethodName != null) {
                    // check if the data is upload in the database
                    $existingAnalyte = $entmanager->getRepository(Analyte::class)->findOneBy(['analyteCode' => $analyteCode]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingAnalyte) {
                        $analyte = new Analyte();
                        if ($this->getUser()) {
                            $analyte->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $analyteAnnotationLevel = $entmanager->getRepository(AnnotationLevel::class)->findOneBy(['ontology_id' => $annotationLevelOntId]);
                            if (($analyteAnnotationLevel != null) && ($analyteAnnotationLevel instanceof \App\Entity\AnnotationLevel)) {
                                $analyte->setAnnotationLevel($analyteAnnotationLevel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the annotation level ontology " .$annotationLevelOntId);
                        }
                        
                        try {
                            //code...
                            $analyteIdentificationLevel = $entmanager->getRepository(IdentificationLevel::class)->findOneBy(['ontology_id' => $identificationLevelOntId]);
                            if (($analyteIdentificationLevel != null) && ($analyteIdentificationLevel instanceof \App\Entity\IdentificationLevel)) {
                                $analyte->setIdentificationLevel($analyteIdentificationLevel);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the identification level ontology " .$identificationLevelOntId);
                        }

                        try {
                            //code...
                            $analyteMetaboliteClass = $entmanager->getRepository(MetaboliteClass::class)->findOneBy(['ontology_id' => $metaboliteClassOntId]);
                            if (($analyteMetaboliteClass != null) && ($analyteMetaboliteClass instanceof \App\Entity\MetaboliteClass)) {
                                $analyte->setMetaboliteClass($analyteMetaboliteClass);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the metabolite class ontology " .$metaboliteClassOntId);
                        }

                        try {
                            //code...
                            $analyteHealthflavor = $entmanager->getRepository(AnalyteFlavorHealth::class)->findOneBy(['ontology_id' => $helathFlavorOntId]);
                            if (($analyteHealthflavor != null) && ($analyteHealthflavor instanceof \App\Entity\AnalyteFlavorHealth)) {
                                $analyte->setHealthAndFlavor($analyteHealthflavor);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the health and flavor ontology " .$helathFlavorOntId);
                        }

                        try {
                            //code...
                            $analyteObsVarMethod = $entmanager->getRepository(ObservationVariableMethod::class)->findOneBy(['name' => $obsVariableMethodName]);
                            if (($analyteObsVarMethod != null) && ($analyteObsVarMethod instanceof \App\Entity\ObservationVariableMethod)) {
                                $analyte->setObservationVariableMethod($analyteObsVarMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method name " .$obsVariableMethodName);
                        }

                        try {
                            //code...
                            $analyte->setName($name);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the analyte name " .$name);
                        }

                        try {
                            //code...
                            $analyte->setAnalyteCode($analyteCode);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the analyte code " .$analyteCode);
                        }

                        try {
                            //code...
                            $analyte->setRetentionTime($retentiontTime);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the analyte retention time " .$retentiontTime);
                        }

                        try {
                            //code...
                            $analyte->setMassToChargeRatio($massToChargeRatio);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the analyte mass to charge ratio " .$massToChargeRatio);
                        }
                        $analyte->setIsActive(true);
                        $analyte->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($analyte);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalAnalyteAfter = $repoAnalyte->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAnalyteBefore == 0) {
                $this->addFlash('success', $totalAnalyteAfter . " analytes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAnalyteAfter - $totalAnalyteBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new analyte has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " analyte has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " analytes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('analyte_index'));
        }

        $context = [
            'title' => 'Analyte Upload From Excel',
            'analyteUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('analyte/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/analyte_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'analyte_template_example.xlsx');
        return $response;
       
    }
}

