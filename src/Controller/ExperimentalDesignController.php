<?php

namespace App\Controller;

use App\Entity\ExperimentalDesignType;
use App\Form\ExperimentalDesignCreateType;
use App\Form\ExperimentalDesignUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ExperimentalDesignTypeRepository;
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
 * @Route("experimental/design", name="experimental_design_")
 */
class ExperimentalDesignController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ExperimentalDesignTypeRepository $experimentalDesign): Response
    {
        $experimentalDesigns =  $experimentalDesign->findAll();
        $context = [
            'title' => 'Experimental Design List',
            'experimentalDesigns' => $experimentalDesigns
        ];
        return $this->render('experimental_design/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $experimentalDesign = new ExperimentalDesignType();
        $form = $this->createForm(ExperimentalDesignCreateType::class, $experimentalDesign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $experimentalDesign->setCreatedBy($this->getUser());
            }
            $experimentalDesign->setIsActive(true);
            $experimentalDesign->setCreatedAt(new \DateTime());
            $entmanager->persist($experimentalDesign);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('experimental_design_index'));
        }

        $context = [
            'title' => 'Experimental Design Creation',
            'experimentalDesignForm' => $form->createView()
        ];
        return $this->render('experimental_design/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ExperimentalDesignType $experimentalDesignSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Experimental Design Details',
            'experimentalDesign' => $experimentalDesignSelected
        ];
        return $this->render('experimental_design/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ExperimentalDesignType $experimentalDesign, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('experimental_design_edit', $experimentalDesign);
        $form = $this->createForm(ExperimentalDesignUpdateType::class, $experimentalDesign);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($experimentalDesign);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('experimental_design_index'));
        }

        $context = [
            'title' => 'Experimental Design Update',
            'experimentalDesignForm' => $form->createView()
        ];
        return $this->render('experimental_design/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ExperimentalDesignType $experimentalDesign, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($experimentalDesign->getId()) {
            $experimentalDesign->setIsActive(!$experimentalDesign->getIsActive());
        }
        $entmanager->persist($experimentalDesign);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $experimentalDesign->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('experimentalDesign_home'));
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
            $repoExperimentalDesignType = $entmanager->getRepository(ExperimentalDesignType::class);
            // Query how many rows are there in the developmentalStage table
            $totalExperimentalDesignTypeBefore = $repoExperimentalDesignType->createQueryBuilder('tab')
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
                    $existingExperimentalDesignType = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingExperimentalDesignType) {
                        $experimentalDesignType = new ExperimentalDesignType();
                        if ($this->getUser()) {
                            $experimentalDesignType->setCreatedBy($this->getUser());
                        }
                        $experimentalDesignType->setOntologyId($ontology_id);
                        $experimentalDesignType->setName($name);
                        if ($description != null) {
                            $experimentalDesignType->setDescription($description);
                        }
                        
                        if ($parentTermString != null) {
                            $experimentalDesignType->setParOnt($parentTermString);
                        }
                        
                        $experimentalDesignType->setIsActive(true);
                        $experimentalDesignType->setCreatedAt(new \DateTime());
                        $entmanager->persist($experimentalDesignType);
                        $entmanager->flush();
                    }
                }
            }
            // $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['ontology_id' => $parentTerm]);
                    //$ontologyIdParentTermDes = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['par_ont' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\ExperimentalDesignType)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        if ($stringParentTerm != null) {
                            $parentTermId = $stringParentTerm->getId();
                            if ($parentTermId != null) {
                                $resInsert = $connexion->executeStatement("INSERT experimental_design_type_experimental_design_type VALUES('$ontId', '$parentTermId')");
                                $resInsert1 = $connexion->executeStatement('UPDATE experimental_design_type SET is_poau = ? WHERE id = ?', [1, $parentTermId]);
                            }
                        }
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalExperimentalDesignTypeAfter = $repoExperimentalDesignType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalExperimentalDesignTypeBefore == 0) {
                $this->addFlash('success', $totalExperimentalDesignTypeAfter . " experimental designs have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalExperimentalDesignTypeAfter - $totalExperimentalDesignTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new experimental design has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " experimental design has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " experimental designs have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('experimental_design_index'));
        }

        $context = [
            'title' => 'Experimental Design TypeUpload From Excel',
            'experimentalDesignTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('experimental_design/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/experimental_design_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'experimental_design_type_template_example.xls');
        return $response;
       
    }
}