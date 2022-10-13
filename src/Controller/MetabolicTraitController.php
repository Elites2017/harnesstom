<?php

namespace App\Controller;

use App\Entity\MetabolicTrait;
use App\Form\MetabolicTraitType;
use App\Form\MetabolicTraitUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MetabolicTraitRepository;
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
 * @Route("/metabolic/trait", name="metabolic_trait_")
 */
class MetabolicTraitController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetabolicTraitRepository $metabolicTraitRepo): Response
    {
        $metabolicTraits =  $metabolicTraitRepo->findAll();
        $context = [
            'title' => 'Metabolic Trait List',
            'metabolicTraits' => $metabolicTraits
        ];
        return $this->render('metabolic_trait/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metabolicTrait = new MetabolicTrait();
        $form = $this->createForm(MetabolicTraitType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metabolicTrait->setCreatedBy($this->getUser());
            }
            $metabolicTrait->setIsActive(true);
            $metabolicTrait->setCreatedAt(new \DateTime());
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Creation',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MetabolicTrait $metabolicTraitSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolic Trait Details',
            'metabolicTrait' => $metabolicTraitSelected
        ];
        return $this->render('metabolic_trait/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolic_trait_edit', $metabolicTrait);
        $form = $this->createForm(MetabolicTraitUpdateType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Update',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metabolicTrait->getId()) {
            $metabolicTrait->setIsActive(!$metabolicTrait->getIsActive());
        }
        $entmanager->persist($metabolicTrait);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metabolicTrait->getIsActive()
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
            $repoTrait = $entmanager->getRepository(MetabolicTrait::class);
            // Query how many rows are there in the trait table
            $totalTraitBefore = $repoTrait->createQueryBuilder('tab')
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
                $ontology_id = $row['A'];
                $name = $row['B'];
                $description = $row['C'];
                $chebiMass = $row['D'];
                $chebiMonoscopic = $row['E'];
                $synonym = $row['F'];
                $chebiLink = $row['G'];
                $parentTerm = $row['I'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingMetabolicTrait = $entmanager->getRepository(MetabolicTrait::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingMetabolicTrait) {
                        $metabolicTrait = new MetabolicTrait();
                        if ($this->getUser()) {
                            $metabolicTrait->setCreatedBy($this->getUser());
                        }
                        $metabolicTrait->setOntologyId($ontology_id);
                        $metabolicTrait->setName($name);
                        if ($description != null) {
                            $metabolicTrait->setDescription($description);
                        }
                        if ($parentTerm != null) {
                            $metabolicTrait->setParOnt($parentTerm);
                        }
                        $metabolicTrait->setChebiMass($chebiMass);
                        $metabolicTrait->setChebiMonoIsoTopicMass($chebiMonoscopic);
                        // split the text to array based on that patern
                        $synonym = explode("|", $synonym);
                        //var_dump($chebiMonoscopic);
                        //$arr [] = $synonym;
                        //dd($arr);
                        $metabolicTrait->setSynonym($synonym);
                        $metabolicTrait->setChebiLink($chebiLink);

                        $metabolicTrait->setIsActive(true);
                        $metabolicTrait->setCreatedAt(new \DateTime());
                        $entmanager->persist($metabolicTrait);
                        $entmanager->flush();
                    }
                }
            }
            
            $totalTraitAfter = $repoTrait->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalTraitBefore == 0) {
                $this->addFlash('success', $totalTraitAfter . " metabolic traits have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalTraitAfter - $totalTraitBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new metabolic trait has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolic trait has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolic traits have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Upload From Excel',
            'metabolicTraitUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/upload_from_excel.html.twig', $context);
    }

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel-mtm", name="upload_from_excel_mtm")
     */
    public function uploadManyToManyFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
                    $this->addFlash('danger', "Fail to upload the file, try again");
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
            // get the connection
            $connexion = $entmanager->getConnection();
            // to count the number of affected rows.
            $counter = 0;
            // loop over the array to get each row
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['B'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null) {
                    // check if the data is upload in the database
                    $ontMetabolicTraitEnt = $entmanager->getRepository(MetabolicTrait::class)->findOneBy(['ontology_id' => $ontology_id]);
                    if ($ontMetabolicTraitEnt) {
                        $ontologyIdDbId = $ontMetabolicTraitEnt->getId();
                        $parentTermMetabolicTraitEnt = $entmanager->getRepository(MetabolicTrait::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if ($parentTermMetabolicTraitEnt) {
                            $parentTermDbId = $parentTermMetabolicTraitEnt->getId();
                            // check if this ID couple is already in the database, otherwise insert it in.
                            $result = $connexion->executeStatement('SELECT metabolic_trait_source FROM metabolic_trait_metabolic_trait WHERE metabolic_trait_source = ? AND metabolic_trait_target = ?', [$parentTermDbId, $ontologyIdDbId]);
                            //dd($parentTermId);
                            if ($result == 0) {
                                $resInsert = $connexion->executeStatement("INSERT INTO metabolic_trait_metabolic_trait VALUES('$parentTermDbId', '$ontologyIdDbId')");
                                if ($resInsert == 1) {
                                    $counter += 1;
                                }
                            }
                        } else {
                            $this->addFlash('danger', "Error this parent term $parentTerm has not been saved / used in the table trait entity as an ontologyId before, make sure it has been already saved in the trait entity as an ontologyId before a being used as a parent temtable and try again");
                        }
                    } else {
                        $this->addFlash('danger', "Error this ontology_id $ontology_id is not in the database, make sure it has been already saved in the trait entity table and try again");
                    }
                }
            }
            if ($counter <= 1) {
                $this->addFlash('success', " $counter " ."row affected ");    
            } else {
                $this->addFlash('success', " $counter " ."rows affected ");            
            }
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Many To Many Upload From Excel',
            'metabolicTraitMTMUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/upload_from_excel_mtm.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/metabolic_trait_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'metabolic_trait_template_example.xls');
        return $response;
       
    }
}

