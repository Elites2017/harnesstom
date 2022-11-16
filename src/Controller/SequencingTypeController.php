<?php

namespace App\Controller;

use App\Entity\SequencingType;
use App\Form\SequencingCreateType;
use App\Form\SequencingUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SequencingTypeRepository;
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
 * @Route("/sequencing/type", name="sequencing_type_")
 */
class SequencingTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SequencingTypeRepository $sequencingTypeRepo): Response
    {
        $sequencingTypes =  $sequencingTypeRepo->findAll();
        $context = [
            'title' => 'Sequencing Type List',
            'sequencingTypes' => $sequencingTypes
        ];
        return $this->render('sequencing_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sequencingType = new SequencingType();
        $form = $this->createForm(SequencingCreateType::class, $sequencingType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $sequencingType->setCreatedBy($this->getUser());
            }
            $sequencingType->setIsActive(true);
            $sequencingType->setCreatedAt(new \DateTime());
            $entmanager->persist($sequencingType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_type_index'));
        }

        $context = [
            'title' => 'Sequencing Type Creation',
            'sequencingTypeForm' => $form->createView()
        ];
        return $this->render('sequencing_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(SequencingType $sequencingTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sequencing Type Details',
            'sequencingType' => $sequencingTypeSelected
        ];
        return $this->render('sequencing_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(SequencingType $sequencingType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('sequencing_type_edit', $sequencingType);
        $form = $this->createForm(SequencingUpdateType::class, $sequencingType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($sequencingType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('sequencing_type_index'));
        }

        $context = [
            'title' => 'Sequencing Type Update',
            'sequencingTypeForm' => $form->createView()
        ];
        return $this->render('sequencing_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(SequencingType $sequencingType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sequencingType->getId()) {
            $sequencingType->setIsActive(!$sequencingType->getIsActive());
        }
        $entmanager->persist($sequencingType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sequencingType->getIsActive()
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
            $repoSequencingType = $entmanager->getRepository(SequencingType::class);
            // Query how many rows are there in the SequencingType table
            $totalSequencingTypeBefore = $repoSequencingType->createQueryBuilder('tab')
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
                    $existingSequencingType = $entmanager->getRepository(SequencingType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingSequencingType) {
                        $sequencingType = new SequencingType();
                        if ($this->getUser()) {
                            $sequencingType->setCreatedBy($this->getUser());
                        }
                        $sequencingType->setOntologyId($ontology_id);
                        $sequencingType->setName($name);
                        if ($description != null) {
                            $sequencingType->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $sequencingType->setParOnt($parentTermString);
                        }
                        $sequencingType->setIsActive(true);
                        $sequencingType->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($sequencingType);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(SequencingType::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\SequencingType)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(SequencingType::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE sequencing_type SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                } 
            }
            
            // Query how many rows are there in the Country table
            $totalSequencingTypeAfter = $repoSequencingType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSequencingTypeBefore == 0) {
                $this->addFlash('success', $totalSequencingTypeAfter . " sequencing types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSequencingTypeAfter - $totalSequencingTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new sequencing type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " sequencing type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " sequencing types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('sequencing_type_index'));
        }

        $context = [
            'title' => 'Sequencing Type Upload From Excel',
            'sequencingTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('sequencing_type/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/sequencing_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'sequencing_type_template_example.xls');
        return $response;
       
    }
}

