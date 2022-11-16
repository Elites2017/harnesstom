<?php

/* 
    This is the factorTypeController which contains the CRUD method of this object.
    1. The index function is to list all the object from the DB
    
    2. The create function is to create the object by
        2.1 initializes the object
        2.2 create the form from the factorTypeType form and do the binding
        2.2.1 pass the request to the form to handle it
        2.2.2 Analyze the form, if everything is okay, save the object and redirect the user
        if there is any problem, the same page will be display to the user with the context
    
    3. The details function is just to show the details of the selected object to the user.

    4. the update funtion is a little bit similar with the create one, because they almost to the same thing, but
    in the update, we don't initialize the object as it will come from the injection and it is supposed to be existed.

    5. the delete function is to delete the object from the DB, but to keep a trace, it is preferable
    to just change the state of the object.

    March 11, 2022
    David PIERRE
*/

namespace App\Controller;

use App\Entity\FactorType;
use App\Form\FactorCreateType;
use App\Form\FactorUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\FactorTypeRepository;
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
 * @Route("factor/type", name="factor_type_")
 */
class FactorTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(FactorTypeRepository $factorTypeRepo): Response
    {
        $factorTypes =  $factorTypeRepo->findAll();
        $context = [
            'title' => 'FactorType List',
            'factorTypes' => $factorTypes
        ];
        return $this->render('factor_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $factorType = new FactorType();
        $form = $this->createForm(FactorCreateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $factorType->setCreatedBy($this->getUser());
            }
            $factorType->setIsActive(true);
            $factorType->setCreatedAt(new \DateTime());
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Creation',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(FactorType $factorTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'FactorType Details',
            'factorType' => $factorTypeSelected
        ];
        return $this->render('factor_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(FactorType $factorType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('factor_type_edit', $factorType);
        $form = $this->createForm(FactorUpdateType::class, $factorType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($factorType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'FactorType Update',
            'factorTypeForm' => $form->createView()
        ];
        return $this->render('factor_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(FactorType $factorType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($factorType->getId()) {
            $factorType->setIsActive(!$factorType->getIsActive());
        }
        $entmanager->persist($factorType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $factorType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('factorType_home'));
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
            $repoFactorType = $entmanager->getRepository(FactorType::class);
            // Query how many rows are there in the FactorType table
            $totalFactorTypeBefore = $repoFactorType->createQueryBuilder('tab')
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
            // loop over the array to get each row
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $name = $row['B'];
                $description = $row['C'];
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingFactorType = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingFactorType) {
                        $factorType = new FactorType();
                        if ($this->getUser()) {
                            $factorType->setCreatedBy($this->getUser());
                        }
                        $factorType->setOntologyId($ontology_id);
                        $factorType->setName($name);
                        if ($description != null) {
                            $factorType->setDescription($description);
                        }
                        
                        if ($parentTermString != null) {
                            $factorType->setParOnt($parentTermString);
                        }
                        
                        $factorType->setIsActive(true);
                        $factorType->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($factorType);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // $entmanager->flush();
            // get the connection
            // $connexion = $entmanager->getConnection();
            // // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            // foreach ($sheetData as $key => $row) {
            //     $ontology_id = $row['A'];
            //     $parentTerm = $row['D'];
            //     // check if the file doesn't have empty columns
            //     if ($ontology_id != null && $parentTerm != null ) {
            //         // check if the data is upload in the database
            //         $ontologyIdParentTerm = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $parentTerm]);
            //         //$ontologyIdParentTermDes = $entmanager->getRepository(FactorType::class)->findOneBy(['par_ont' => $parentTerm]);
            //         if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\FactorType)) {
            //             $ontId = $ontologyIdParentTerm->getId();
            //             // get the real string (parOnt) parent term or its line id so that to do the link 
            //             $stringParentTerm = $entmanager->getRepository(FactorType::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
            //             if ($stringParentTerm != null) {
            //                 $parentTermId = $stringParentTerm->getId();
            //                 if ($parentTermId != null) {
            //                     $resInsert = $connexion->executeStatement("INSERT factor_type_factor_type VALUES('$ontId', '$parentTermId')");
            //                     $resInsert1 = $connexion->executeStatement('UPDATE factor_type SET is_poau = ? WHERE id = ?', [1, $parentTermId]);
            //                 }
            //             }
            //         }
            //     }
            // }

            // Query how many rows are there in the Country table
            $totalFactorTypeAfter = $repoFactorType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalFactorTypeBefore == 0) {
                $this->addFlash('success', $totalFactorTypeAfter . " factor types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalFactorTypeAfter - $totalFactorTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new factor type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " factor type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " factor types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Type Upload From Excel',
            'factorTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('factor_type/upload_from_excel.html.twig', $context);
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
                    $ontFactorTypeEnt = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    if ($ontFactorTypeEnt) {
                        $ontologyIdDbId = $ontFactorTypeEnt->getId();
                        $parentTermFactorTypeEnt = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if ($parentTermFactorTypeEnt) {
                            $parentTermDbId = $parentTermFactorTypeEnt->getId();
                            // check if this ID couple is already in the database, otherwise insert it in.
                            $result = $connexion->executeStatement('SELECT factor_type_source FROM factor_type_factor_type WHERE factor_type_source = ? AND factor_type_target = ?', [$parentTermDbId, $ontologyIdDbId]);
                            //dd($parentTermId);
                            if ($result == 0) {
                                $resInsert = $connexion->executeStatement("INSERT INTO factor_type_factor_type VALUES('$parentTermDbId', '$ontologyIdDbId')");
                                if ($resInsert == 1) {
                                    $counter += 1;
                                }
                            }
                        } else {
                            $this->addFlash('danger', "Error this parent term $parentTerm has not been saved / used in the table factor type entity as an ontologyId before, make sure it has been already saved in the factor type entity as an ontologyId before a being used as a parent temtable and try again");
                        }
                    } else {
                        $this->addFlash('danger', "Error this ontology_id $ontology_id is not in the database, make sure it has been already saved in the factor type entity table and try again");
                    }
                }
            }
            if ($counter <= 1) {
                $this->addFlash('success', " $counter " ."row affected ");    
            } else {
                $this->addFlash('success', " $counter " ."rows affected ");            
            }
            return $this->redirect($this->generateUrl('factor_type_index'));
        }

        $context = [
            'title' => 'Factor Type Entity Many To Many Upload From Excel',
            'factorTypeMTMUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('factor_type/upload_from_excel_mtm.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/factor_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'factor_type_template_example.xls');
        return $response;
       
    }
}
