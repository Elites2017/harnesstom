<?php

namespace App\Controller;

use App\Entity\CollectingSource;
use App\Repository\CollectingSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CollectingSourceType;
use App\Form\CollectingSourceUpdateType;
use App\Form\UploadFromExcelType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("collecting/source", name="collecting_source_")
 */
class CollectingSourceController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectingSourceRepository $collectingSourceRepo): Response
    {
        $collectingSources =  $collectingSourceRepo->findAll();
        $parentsOnly = $collectingSourceRepo->getParentsOnly();
        $context = [
            'title' => 'Collecting Source List',
            'collectingSources' => $collectingSources,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('collecting_source/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collectingSource = new CollectingSource();
        $form = $this->createForm(CollectingSourceType::class, $collectingSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $collectingSource->setCreatedBy($this->getUser());
            }
            $collectingSource->setIsActive(true);
            $collectingSource->setCreatedAt(new \DateTime());
            $entmanager->persist($collectingSource);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_source_index'));
        }

        $context = [
            'title' => 'Collecting Source Creation',
            'collectingSourceForm' => $form->createView()
        ];
        return $this->render('collecting_source/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(collectingSource $collectingSourceselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collecting Source Details',
            'collectingSource' => $collectingSourceselected
        ];
        return $this->render('collecting_source/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectingSource $collectingSource, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('collecting_source_edit', $collectingSource);
        $form = $this->createForm(CollectingSourceUpdateType::class, $collectingSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collectingSource);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_source_index'));
        }

        $context = [
            'title' => 'Collecting Source Update',
            'collectingSourceForm' => $form->createView()
        ];
        return $this->render('collecting_source/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectingSource $collectingSource, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collectingSource->getId()) {
            $collectingSource->setIsActive(!$collectingSource->getIsActive());
        }
        $entmanager->persist($collectingSource);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collectingSource->getIsActive()
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
            $repoCollectingSource = $entmanager->getRepository(CollectingSource::class);
            // Query how many rows are there in the CollectingSource table
            $totalCollectingSourceBefore = $repoCollectingSource->createQueryBuilder('tab')
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
                    $existingCollectingSource = $entmanager->getRepository(CollectingSource::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingCollectingSource) {
                        $collectingSource = new CollectingSource();
                        if ($this->getUser()) {
                            $collectingSource->setCreatedBy($this->getUser());
                        }
                        $collectingSource->setOntologyId($ontology_id);
                        $collectingSource->setName($name);
                        if ($description != null) {
                            $collectingSource->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $collectingSource->setParOnt($parentTermString);
                        }
                        $collectingSource->setIsActive(true);
                        $collectingSource->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($collectingSource);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(CollectingSource::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\CollectingSource)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(CollectingSource::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE collecting_source SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalCollectingSourceAfter = $repoCollectingSource->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCollectingSourceBefore == 0) {
                $this->addFlash('success', $totalCollectingSourceAfter . " collecting sources have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCollectingSourceAfter - $totalCollectingSourceBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new collecting source has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " collecting source has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " collecting sources have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('collecting_source_index'));
        }

        $context = [
            'title' => 'Collecting Source Upload From Excel',
            'collectingSourceUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('collecting_source/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/collecting_source_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'collecting_source_template_example.xls');
        return $response;
       
    }
}


