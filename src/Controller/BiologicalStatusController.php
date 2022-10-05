<?php

namespace App\Controller;

use App\Entity\BiologicalStatus;
use App\Form\BiologicalStatusType;
use App\Form\BiologicalStatusUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\BiologicalStatusRepository;
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
 * @Route("biological/status", name="biological_status_")
 */
class BiologicalStatusController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BiologicalStatusRepository $biologicalStatusRepo): Response
    {
        $biologicalStatuses =  $biologicalStatusRepo->findAll();
        $context = [
            'title' => 'Biological Status List',
            'biologicalStatuses' => $biologicalStatuses
        ];
        return $this->render('biological_status/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $biologicalStatus = new BiologicalStatus();
        $form = $this->createForm(BiologicalStatusType::class, $biologicalStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $biologicalStatus->setCreatedBy($this->getUser());
            }
            $biologicalStatus->setIsActive(true);
            $biologicalStatus->setCreatedAt(new \DateTime());
            $entmanager->persist($biologicalStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('biological_status_index'));
        }

        $context = [
            'title' => 'Biological Status',
            'biologicalStatusForm' => $form->createView()
        ];
        return $this->render('biological_status/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(BiologicalStatus $biologicalStatusSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Biological Status',
            'biologicalStatus' => $biologicalStatusSelected
        ];
        return $this->render('biological_status/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(BiologicalStatus $biologicalStatus, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('biological_status_edit', $biologicalStatus);
        $form = $this->createForm(BiologicalStatusUpdateType::class, $biologicalStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($biologicalStatus);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('biological_status_index'));
        }

        $context = [
            'title' => 'Biologiccal Status Update',
            'biologicalStatusForm' => $form->createView()
        ];
        return $this->render('biological_status/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(BiologicalStatus $biologicalStatus, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($biologicalStatus->getId()) {
            $biologicalStatus->setIsActive(!$biologicalStatus->getIsActive());
        }
        $entmanager->persist($biologicalStatus);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $biologicalStatus->getIsActive()
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
            $repoBiologicalStatus = $entmanager->getRepository(BiologicalStatus::class);
            // Query how many rows are there in the BiologicalStatus table
            $totalBiologicalStatusBefore = $repoBiologicalStatus->createQueryBuilder('tab')
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
                    $existingBiologicalStatus = $entmanager->getRepository(BiologicalStatus::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingBiologicalStatus) {
                        $biologicalStatus = new BiologicalStatus();
                        if ($this->getUser()) {
                            $biologicalStatus->setCreatedBy($this->getUser());
                        }
                        $biologicalStatus->setOntologyId($ontology_id);
                        $biologicalStatus->setName($name);
                        if ($description != null) {
                            $biologicalStatus->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $biologicalStatus->setParOnt($parentTermString);
                        }
                        $biologicalStatus->setIsActive(true);
                        $biologicalStatus->setCreatedAt(new \DateTime());
                        $entmanager->persist($biologicalStatus);
                    }
                }
            }
            $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(BiologicalStatus::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\BiologicalStatus)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(BiologicalStatus::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE biological_status SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalBiologicalStatusAfter = $repoBiologicalStatus->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalBiologicalStatusBefore == 0) {
                $this->addFlash('success', $totalBiologicalStatusAfter . " biological statuses have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalBiologicalStatusAfter - $totalBiologicalStatusBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new biological status has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " biological status has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " biological statuses have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('biological_status_index'));
        }

        $context = [
            'title' => 'Biological Status Upload From Excel',
            'biologicalStatusUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('biological_status/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/biological_status_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'biological_status_template_example.xls');
        return $response;
       
    }
}


