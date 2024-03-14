<?php

namespace App\Controller;

use App\Entity\MLSStatus;
use App\Form\MLSStatusType;
use App\Form\MLSStatusUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MLSStatusRepository;
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
 * @Route("/mls/status", name="mls_status_")
 */
class MLSStatusController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MLSStatusRepository $mlsStatusRepo): Response
    {
        $mlsStatuses =  $mlsStatusRepo->findAll();
        $parentsOnly = $mlsStatusRepo->getParentsOnly();
        $context = [
            'title' => 'MLS Staus List',
            'mlsStatuses' => $mlsStatuses,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('mls_status/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mlsStatus = new MLSStatus();
        $form = $this->createForm(MLSStatusType::class, $mlsStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $mlsStatus->setCreatedBy($this->getUser());
            }
            $mlsStatus->setIsActive(true);
            $mlsStatus->setCreatedAt(new \DateTime());
            $entmanager->persist($mlsStatus);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('mls_status_index'));
        }

        $context = [
            'title' => 'MLS Staus Creation',
            'mlsStatusForm' => $form->createView()
        ];
        return $this->render('mls_status/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MLSStatus $mlsStatusSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'MLS Staus Details',
            'mlsStatus' => $mlsStatusSelected
        ];
        return $this->render('mls_status/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MLSStatus $mlsStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('mls_status_edit', $mlsStatus);
        $form = $this->createForm(MLSStatusUpdateType::class, $mlsStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($mlsStatus);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('mls_status_index'));
        }

        $context = [
            'title' => 'MLS Staus Update',
            'mlsStatusForm' => $form->createView()
        ];
        return $this->render('mls_status/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MLSStatus $mlsStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($mlsStatus->getId()) {
            $mlsStatus->setIsActive(!$mlsStatus->getIsActive());
        }
        $entmanager->persist($mlsStatus);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $mlsStatus->getIsActive()
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
            $repoMLSStatus = $entmanager->getRepository(MLSStatus::class);
            // Query how many rows are there in the MLSStatus table
            $totalMLSStatusBefore = $repoMLSStatus->createQueryBuilder('tab')
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
                if ($ontology_id !== null && $name != null) {
                    // check if the data is upload in the database
                    $existingMLSStatus = $entmanager->getRepository(MLSStatus::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingMLSStatus) {
                        $mLSStatus = new MLSStatus();
                        if ($this->getUser()) {
                            $mLSStatus->setCreatedBy($this->getUser());
                        }
                        $mLSStatus->setOntologyId($ontology_id);
                        $mLSStatus->setName($name);
                        if ($description != null) {
                            $mLSStatus->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $mLSStatus->setParOnt($parentTermString);
                        }
                        $mLSStatus->setIsActive(true);
                        $mLSStatus->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($mLSStatus);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(MLSStatus::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\MLSStatus)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(MLSStatus::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE mlsstatus SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalMLSStatusAfter = $repoMLSStatus->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMLSStatusBefore == 0) {
                $this->addFlash('success', $totalMLSStatusAfter . " mls statuses have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMLSStatusAfter - $totalMLSStatusBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new mls status has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " mls status has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " mls statuses have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('mls_status_index'));
        }

        $context = [
            'title' => 'mls status Upload From Excel',
            'mLSStatusUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('mls_status/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/mls_status_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'mls_status_template_example.xls');
        return $response;
       
    }
}
