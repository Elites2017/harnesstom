<?php

namespace App\Controller;

use App\Entity\DevelopmentalStage;
use App\Form\DevelomentalStageType;
use App\Form\DevelomentalStageUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\DevelopmentalStageRepository;
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
 * @Route("developmental/stage", name="developmental_stage_")
 */
class DevelopmentalStageController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DevelopmentalStageRepository $developmentalStageRepo): Response
    {
        $developmentalStages =  $developmentalStageRepo->findAll();
        $context = [
            'title' => 'Developmental Stage List',
            'developmentalStages' => $developmentalStages
        ];
        return $this->render('developmental_stage/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $developmentalStage = new DevelopmentalStage();
        $form = $this->createForm(DevelomentalStageType::class, $developmentalStage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $developmentalStage->setCreatedBy($this->getUser());
            }
            $developmentalStage->setIsActive(true);
            $developmentalStage->setCreatedAt(new \DateTime());
            $entmanager->persist($developmentalStage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('developmental_stage_index'));
        }

        $context = [
            'title' => 'Developmental Stage Creation',
            'developmentalStageForm' => $form->createView()
        ];
        return $this->render('developmental_stage/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(DevelopmentalStage $developmentalStageSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Developmental Stage Details',
            'developmentalStage' => $developmentalStageSelected
        ];
        return $this->render('developmental_stage/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(DevelopmentalStage $developmentalStage, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('developmental_stage_edit', $developmentalStage);
        $form = $this->createForm(DevelomentalStageUpdateType::class, $developmentalStage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($developmentalStage);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('developmental_stage_index'));
        }

        $context = [
            'title' => 'Developmental Stage Update',
            'developmentalStageForm' => $form->createView()
        ];
        return $this->render('developmental_stage/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(DevelopmentalStage $developmentalStage, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($developmentalStage->getId()) {
            $developmentalStage->setIsActive(!$developmentalStage->getIsActive());
        }
        $entmanager->persist($developmentalStage);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $developmentalStage->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('developmentalStage_home'));
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
            $repodevelopmentalStage = $entmanager->getRepository(DevelopmentalStage::class);
            // Query how many rows are there in the developmentalStage table
            $totalDevelopmentalStageBefore = $repodevelopmentalStage->createQueryBuilder('tab')
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
                    $existingDevelopmentalStage = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingDevelopmentalStage) {
                        $developmentalStage = new DevelopmentalStage();
                        if ($this->getUser()) {
                            $developmentalStage->setCreatedBy($this->getUser());
                        }
                        $developmentalStage->setOntologyId($ontology_id);
                        $developmentalStage->setName($name);
                        if ($description != null) {
                            $developmentalStage->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $developmentalStage->setParOnt($parentTermString);
                        }
                        $developmentalStage->setIsActive(true);
                        $developmentalStage->setCreatedAt(new \DateTime());
                        $entmanager->persist($developmentalStage);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['ontology_id' => $parentTerm]);
                    //$ontologyIdParentTermDes = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['par_ont' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\DevelopmentalStage)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(DevelopmentalStage::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        if ($stringParentTerm != null) {
                            $parentTermId = $stringParentTerm->getId();
                            if ($parentTermId != null) {
                                $resInsert = $connexion->executeStatement("INSERT developmental_stage_developmental_stage VALUES('$ontId', '$parentTermId')");
                                $resInsert1 = $connexion->executeStatement('UPDATE developmental_stage SET is_poau = ? WHERE id = ?', [1, $parentTermId]);
                            }
                        }
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalDevelopmentalStageAfter = $repodevelopmentalStage->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalDevelopmentalStageBefore == 0) {
                $this->addFlash('success', $totalDevelopmentalStageAfter . " developmental stages have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalDevelopmentalStageAfter - $totalDevelopmentalStageBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new developmental stage has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " developmental stage has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " developmental stages have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('developmental_stage_index'));
        }

        $context = [
            'title' => 'Developmental Stage Upload From Excel',
            'developmentalStageUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('developmental_stage/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/developmental_stage_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'developmental_stage_template_example.xls');
        return $response;
       
    }
}
