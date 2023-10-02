<?php

namespace App\Controller;

use App\Entity\CollectionClass;
use App\Entity\Germplasm;
use App\Form\CollectionType;
use App\Form\CollectionUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\CollectionClassRepository;
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
 * @Route("/collection", name="collection_")
 */
class CollectionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectionClassRepository $collectionRepo): Response
    {
        $collections =  $collectionRepo->findAll();
        $context = [
            'title' => 'Collection List',
            'collections' => $collections
        ];
        return $this->render('collection/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collection = new CollectionClass();
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $collection->setCreatedBy($this->getUser());
            }
            $collection->setIsActive(true);
            $collection->setCreatedAt(new \DateTime());
            $entmanager->persist($collection);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collection_index'));
        }

        $context = [
            'title' => 'Collection Creation',
            'collectionForm' => $form->createView()
        ];
        return $this->render('collection/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CollectionClass $collectionSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collection Details',
            'collection' => $collectionSelected
        ];
        return $this->render('collection/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectionClass $collection, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('collection_edit', $collection);
        $form = $this->createForm(CollectionUpdateType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collection);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collection_index'));
        }

        $context = [
            'title' => 'Collection Update',
            'collectionForm' => $form->createView()
        ];
        return $this->render('collection/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectionClass $collection, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collection->getId()) {
            $collection->setIsActive(!$collection->getIsActive());
        }
        $entmanager->persist($collection);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collection->getIsActive()
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
            $repoCollection = $entmanager->getRepository(CollectionClass::class);
            // Query how many rows are there in the Collection table
            $totalCollectionBefore = $repoCollection->createQueryBuilder('tab')
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
                $collectionName = $row['A'];
                $desc = $row['B'];
                $germplasmList = $row['C'];
                $publicationRef = $row['D'];
                // check if the file doesn't have empty columns
                if ($collectionName != null && $germplasmList != null) {
                    // check if the data is upload in the database
                    $existingCollection = $entmanager->getRepository(CollectionClass::class)->findOneBy(['name' => $collectionName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingCollection) {
                        $collection = new CollectionClass();
                        if ($this->getUser()) {
                            $collection->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $collection->setName($collectionName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the collection name " .$collectionName);
                        }

                        if ($desc) {
                            try {
                                //code...
                                $collection->setDescription($desc);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the collection description " .$desc);
                            }   
                        }

                        $publicationRef = explode(";", $publicationRef);
                        
                        try {
                            //code...
                            $collection->setPublicationReference($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the publication reference ");
                        }
                        
                        // germplasm list
                        $germplasmList = explode(";", $germplasmList);
                        foreach ($germplasmList as $oneGermplasm) {
                            # code...
                            try {
                                //code...
                                $collectionGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $oneGermplasm]);
                                if (($collectionGermplasm != null) && ($collectionGermplasm instanceof \App\Entity\Germplasm)) {
                                    $collection->addGermplasm($collectionGermplasm);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the germplasm " .$oneGermplasm);
                            }
                        }

                        $collection->setIsActive(true);
                        $collection->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($collection);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                } else {
                    $this->addFlash('danger', " The collection name and the germplasm list can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalCollectionAfter = $repoCollection->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCollectionBefore == 0) {
                $this->addFlash('success', $totalCollectionAfter . " collections have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCollectionAfter - $totalCollectionBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new collection has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " collection has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " collections have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('collection_index'));
        }

        $context = [
            'title' => 'Collection Upload From Excel',
            'collectionUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('collection/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/collection_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'collection_template_example.xlsx');
        return $response;
       
    }
}
