<?php

namespace App\Controller;

use App\Entity\BreedingMethod;
use App\Entity\Cross;
use App\Entity\Germplasm;
use App\Entity\Institute;
use App\Entity\Study;
use App\Form\CrossType;
use App\Form\CrossUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\CrossRepository;
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
 * @Route("/cross", name="cross_")
 */
class CrossController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CrossRepository $crossRepo): Response
    {
        $crosses = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $crosses = $crossRepo->findAll();
            } else {
                $crosses = $crossRepo->findReleasedTrialStudyCross($this->getUser());
            }
        } else {
            $crosses = $crossRepo->findReleasedTrialStudyCross();
        }
        $context = [
            'title' => 'Cross List',
            'crosses' => $crosses
        ];
        return $this->render('cross/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cross = new Cross();
        $form = $this->createForm(CrossType::class, $cross);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $cross->setCreatedBy($this->getUser());
            }
            $cross->setIsActive(true);
            $cross->setCreatedAt(new \DateTime());
            $entmanager->persist($cross);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('cross_index'));
        }

        $context = [
            'title' => 'Cross Creation',
            'crossForm' => $form->createView()
        ];
        return $this->render('cross/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Cross $crossSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Cross Details',
            'cross' => $crossSelected
        ];
        return $this->render('cross/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Cross $cross, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('cross_edit', $cross);
        $form = $this->createForm(CrossUpdateType::class, $cross);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($cross);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('cross_index'));
        }

        $context = [
            'title' => 'Cross Update',
            'crossForm' => $form->createView()
        ];
        return $this->render('cross/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Cross $cross, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($cross->getId()) {
            $cross->setIsActive(!$cross->getIsActive());
        }
        $entmanager->persist($cross);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $cross->getIsActive()
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
            $repoCross = $entmanager->getRepository(Cross::class);
            // Query how many rows are there in the Cross table
            $totalCrossBefore = $repoCross->createQueryBuilder('tab')
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
                $crossName = $row['B'];
                $description = $row['C'];
                $parent1ID = $row['D'];
                $parent1Type = $row['E'];
                $parent2ID = $row['F'];
                $parent2Type = $row['G'];
                $breedingMethodOntId = $row['H'];
                $bredcode = $row['I'];
                $year = $row['J'];
                $bibliogralRef = $row['K'];
                // check if the file doesn't have empty columns
                if ($crossName != null && $studyAbbreviation != null) {
                    // check if the data is upload in the database
                    $existingCross = $entmanager->getRepository(Cross::class)->findOneBy(['name' => $crossName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingCross) {
                        $cross = new Cross();
                        if ($this->getUser()) {
                            $cross->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $crossInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $bredcode]);
                            if (($crossInstitute != null) && ($crossInstitute instanceof \App\Entity\Institute)) {
                                $cross->setInstitute($crossInstitute);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the bredcode / institute ID " .$bredcode);
                        }
                        
                        try {
                            //code...
                            $crossStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                            if (($crossStudy != null) && ($crossStudy instanceof \App\Entity\Study)) {
                                $cross->setStudy($crossStudy);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study abbreaviation " .$studyAbbreviation);
                        }

                        try {
                            //code...
                            $crossBreedingMOntId = $entmanager->getRepository(BreedingMethod::class)->findOneBy(['ontology_id' => $breedingMethodOntId]);
                            if (($crossBreedingMOntId != null) && ($crossBreedingMOntId instanceof \App\Entity\BreedingMethod)) {
                                $cross->setBreedingMethod($crossBreedingMOntId);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the breeding method ontology Id " .$breedingMethodOntId);
                        }

                        try {
                            //code...
                            $crossParent1 = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $parent1ID]);
                            if (($crossParent1 != null) && ($crossParent1 instanceof \App\Entity\Germplasm)) {
                                $cross->setParent1($crossParent1);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the parent 1 ID " .$parent1ID);
                        }

                        try {
                            //code...
                            $crossParent2 = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $parent2ID]);
                            if (($crossParent2 != null) && ($crossParent2 instanceof \App\Entity\Germplasm)) {
                                $cross->setParent2($crossParent2);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the parent 2 ID " .$parent2ID);
                        }

                        try {
                            //code...
                            if ($crossName) {
                                $cross->setName($crossName);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the cross name " .$crossName);
                        }

                        try {
                            //code...
                            if ($description) {
                                $cross->setDescription($description);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the cross description " .$description);
                        }

                        try {
                            //code...
                            if ($parent1Type) {
                                $cross->setParent1Type($parent1Type);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the parent 1 type " .$parent1Type);
                        }

                        try {
                            //code...
                            if ($parent2Type) {
                                $cross->setParent2Type($parent2Type);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the parent 2 type " .$parent2Type);
                        }

                        try {
                            //code...
                            if ($year) {
                                $cross->setYear($year);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the year " .$year);
                        }

                        try {
                            //code...
                            if ($bibliogralRef) {
                                $bibliogralRef = explode(",", $bibliogralRef);
                                $cross->setPublicationReference($bibliogralRef);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the bibliographical reference " .$bibliogralRef);
                        }

                        $cross->setIsActive(true);
                        $cross->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($cross);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalCrossAfter = $repoCross->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCrossBefore == 0) {
                $this->addFlash('success', $totalCrossAfter . " crosses have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCrossAfter - $totalCrossBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new cross has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " cross has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " crosses have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('cross_index'));
        }

        $context = [
            'title' => 'Cross Upload From Excel',
            'crossUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('cross/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/cross_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'cross_template_example.xlsx');
        return $response;
       
    }
}

