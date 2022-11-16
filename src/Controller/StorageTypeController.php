<?php

namespace App\Controller;

use App\Entity\StorageType;
use App\Form\StorageCreateType;
use App\Form\StorageUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\StorageTypeRepository;
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
 * @Route("storage/type", name="storage_type_")
 */
class StorageTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StorageTypeRepository $storageTypeRepo): Response
    {
        $storageTypes =  $storageTypeRepo->findAll();
        $context = [
            'title' => 'Storage Type List',
            'storageTypes' => $storageTypes
        ];
        return $this->render('storage_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $storageType = new StorageType();
        $form = $this->createForm(StorageCreateType::class, $storageType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $storageType->setCreatedBy($this->getUser());
            }
            $storageType->setIsActive(true);
            $storageType->setCreatedAt(new \DateTime());
            $entmanager->persist($storageType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('storage_type_index'));
        }

        $context = [
            'title' => 'Storage Type Creation',
            'storageTypeForm' => $form->createView()
        ];
        return $this->render('storage_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(StorageType $storageTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Storage Type Details',
            'storageType' => $storageTypeSelected
        ];
        return $this->render('storage_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(StorageType $storageType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('storage_type_edit', $storageType);
        $form = $this->createForm(StorageUpdateType::class, $storageType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($storageType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('storage_type_index'));
        }

        $context = [
            'title' => 'Storage Type Update',
            'storageTypeForm' => $form->createView()
        ];
        return $this->render('storage_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(StorageType $storageType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($storageType->getId()) {
            $storageType->setIsActive(!$storageType->getIsActive());
        }
        $entmanager->persist($storageType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $storageType->getIsActive()
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
            $repoStorageType = $entmanager->getRepository(StorageType::class);
            // Query how many rows are there in the storage type table
            $totalStorageTypeBefore = $repoStorageType->createQueryBuilder('tab')
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
                    $existingStorageType = $entmanager->getRepository(StorageType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingStorageType) {
                        $storageType = new StorageType();
                        if ($this->getUser()) {
                            $storageType->setCreatedBy($this->getUser());
                        }
                        $storageType->setOntologyId($ontology_id);
                        $storageType->setName($name);
                        if ($description != null) {
                            $storageType->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $storageType->setParOnt($parentTermString);
                        }
                        $storageType->setIsActive(true);
                        $storageType->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($storageType);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(StorageType::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\storageType)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(StorageType::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE storage_type SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            $totalStorageTypeAfter = $repoStorageType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalStorageTypeBefore == 0) {
                $this->addFlash('success', $totalStorageTypeAfter . " storage types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalStorageTypeAfter - $totalStorageTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new storage type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " storage type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " storage types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('storage_type_index'));
        }

        $context = [
            'title' => 'Storage Type Upload From Excel',
            'storageTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('storage_type/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/storage_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'storage_type_template_example.xls');
        return $response;
       
    }
}

