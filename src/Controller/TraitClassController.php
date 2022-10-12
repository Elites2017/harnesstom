<?php

namespace App\Controller;

use App\Entity\TraitClass;
use App\Form\TraitClassType;
use App\Form\TraitClassUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\TraitClassRepository;
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
 * @Route("/trait", name="trait_class_")
 */
class TraitClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TraitClassRepository $traitClassRepo): Response
    {
        $traitClasses =  $traitClassRepo->findAll();
        $context = [
            'title' => 'Trait List',
            'traitClasses' => $traitClasses
        ];
        return $this->render('trait_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $traitClass = new TraitClass();
        $form = $this->createForm(TraitClassType::class, $traitClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $traitClass->setCreatedBy($this->getUser());
            }
            $traitClass->setIsActive(true);
            $traitClass->setCreatedAt(new \DateTime());
            $entmanager->persist($traitClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Creation',
            'traitClassForm' => $form->createView()
        ];
        return $this->render('trait_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(TraitClass $traitClasseSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trait Details',
            'traitClass' => $traitClasseSelected
        ];
        return $this->render('trait_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(TraitClass $traitClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('trait_class_edit', $traitClass);
        $form = $this->createForm(TraitClassUpdateType::class, $traitClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($traitClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Update',
            'traitClassForm' => $form->createView()
        ];
        return $this->render('trait_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(TraitClass $traitClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($traitClass->getId()) {
            $traitClass->setIsActive(!$traitClass->getIsActive());
        }
        $entmanager->persist($traitClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $traitClass->getIsActive()
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
            $repoTrait = $entmanager->getRepository(TraitClass::class);
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
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingTraitClass = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingTraitClass) {
                        $traitClass = new TraitClass();
                        if ($this->getUser()) {
                            $traitClass->setCreatedBy($this->getUser());
                        }
                        $traitClass->setOntologyId($ontology_id);
                        $traitClass->setName($name);
                        if ($description != null) {
                            $traitClass->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $traitClass->setParOnt($parentTermString);
                        }
                        $traitClass->setIsActive(true);
                        $traitClass->setCreatedAt(new \DateTime());
                        $entmanager->persist($traitClass);
                        $entmanager->flush();
                    }
                }
            }
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            // foreach ($sheetData as $key => $row) {
            //     $ontology_id = $row['A'];
            //     $parentTerm = $row['D'];
            //     // check if the file doesn't have empty columns
            //     if ($ontology_id != null && $parentTerm != null ) {
            //         // check if the data is upload in the database
            //         $ontologyIdParentTerm = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $parentTerm]);
            //         if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\TraitClass)) {
            //             $ontId = $ontologyIdParentTerm->getId();
            //             // get the real string (parOnt) parent term or its line id so that to do the link 
            //             $stringParentTerm = $entmanager->getRepository(TraitClass::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
            //             $parentTermId = $stringParentTerm->getId();
            //             // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
            //             $res = $connexion->executeStatement('UPDATE trait_class SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
            //         }
            //     }
            // }
            
            $totalTraitAfter = $repoTrait->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalTraitBefore == 0) {
                $this->addFlash('success', $totalTraitAfter . " traits have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalTraitAfter - $totalTraitBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new trait has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " trait has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " traits have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Upload From Excel',
            'traitUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('trait_class/upload_from_excel.html.twig', $context);
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
                    $ontTraitClassEnt = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $ontology_id]);
                    if ($ontTraitClassEnt) {
                        $ontologyIdDbId = $ontTraitClassEnt->getId();
                        $parentTermTraitClassEnt = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if ($parentTermTraitClassEnt) {
                            $parentTermDbId = $parentTermTraitClassEnt->getId();
                            // check if this ID couple is already in the database, otherwise insert it in.
                            $result = $connexion->executeStatement('SELECT trait_class_source FROM trait_class_trait_class WHERE trait_class_source = ? AND trait_class_target = ?', [$parentTermDbId, $ontologyIdDbId]);
                            //dd($parentTermId);
                            if ($result == 0) {
                                $resInsert = $connexion->executeStatement("INSERT INTO trait_class_trait_class VALUES('$parentTermDbId', '$ontologyIdDbId')");
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
            return $this->redirect($this->generateUrl('trait_class_index'));
        }

        $context = [
            'title' => 'Trait Entity Many To Many Upload From Excel',
            'traitClassMTMUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('trait_class/upload_from_excel_mtm.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/trait_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'trait_template_example.xls');
        return $response;
       
    }
}
