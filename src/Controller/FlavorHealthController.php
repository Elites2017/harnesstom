<?php

namespace App\Controller;

use App\Entity\AnalyteFlavorHealth;
use App\Form\FlavorHealthType;
use App\Form\FlavorHealthUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AnalyteFlavorHealthRepository;
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
 * @Route("/analyte/flavor/health", name="flavor_health_")
 */
class FlavorHealthController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AnalyteFlavorHealthRepository $flavorHealthRepo): Response
    {
        $flavorHealths =  $flavorHealthRepo->findAll();
        $context = [
            'title' => 'Analyte Flavor Health List',
            'flavorHealths' => $flavorHealths
        ];
        return $this->render('flavor_health/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $flavorHealth = new AnalyteFlavorHealth();
        $form = $this->createForm(FlavorHealthType::class, $flavorHealth);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $flavorHealth->setCreatedBy($this->getUser());
            }
            $flavorHealth->setIsActive(true);
            $flavorHealth->setCreatedAt(new \DateTime());
            $entmanager->persist($flavorHealth);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('flavor_health_index'));
        }

        $context = [
            'title' => 'Analyte Flavor Health Creation',
            'flavorHealthForm' => $form->createView()
        ];
        return $this->render('flavor_health/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AnalyteFlavorHealth $flavorHealthSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Analyte Flavor Health Details',
            'flavorHealth' => $flavorHealthSelected
        ];
        return $this->render('flavor_health/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AnalyteFlavorHealth $flavorHealth, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('flavor_health_edit', $flavorHealth);
        $form = $this->createForm(FlavorHealthUpdateType::class, $flavorHealth);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($flavorHealth);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('flavor_health_index'));
        }

        $context = [
            'title' => 'Analyte Flavor Health Update',
            'flavorHealthForm' => $form->createView()
        ];
        return $this->render('flavor_health/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AnalyteFlavorHealth $flavorHealth, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($flavorHealth->getId()) {
            $flavorHealth->setIsActive(!$flavorHealth->getIsActive());
        }
        $entmanager->persist($flavorHealth);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $flavorHealth->getIsActive()
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
            $repoanalyteFlavorHealth = $entmanager->getRepository(AnalyteFlavorHealth::class);
            // Query how many rows are there in the analyteFlavorHealth table
            $totalAnalyteFlavorHealthBefore = $repoanalyteFlavorHealth->createQueryBuilder('tab')
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
                    $existinganalyteFlavorHealth = $entmanager->getRepository(AnalyteFlavorHealth::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existinganalyteFlavorHealth) {
                        $analyteFlavorHealth = new analyteFlavorHealth();
                        if ($this->getUser()) {
                            $analyteFlavorHealth->setCreatedBy($this->getUser());
                        }
                        $analyteFlavorHealth->setOntologyId($ontology_id);
                        $analyteFlavorHealth->setName($name);
                        if ($description != null) {
                            $analyteFlavorHealth->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $analyteFlavorHealth->setParOnt($parentTermString);
                        }
                        $analyteFlavorHealth->setIsActive(true);
                        $analyteFlavorHealth->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($analyteFlavorHealth);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(AnalyteFlavorHealth::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\AnalyteFlavorHealth)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(AnalyteFlavorHealth::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE analyte_flavor_health SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalAnalyteFlavorHealthAfter = $repoanalyteFlavorHealth->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAnalyteFlavorHealthBefore == 0) {
                $this->addFlash('success', $totalAnalyteFlavorHealthAfter . " analyte flavor healths have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAnalyteFlavorHealthAfter - $totalAnalyteFlavorHealthBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new analyte flavor health has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " analyte flavor health has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " analyte flavor healths have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('flavor_health_index'));
        }

        $context = [
            'title' => 'Analyte flavor health Upload From Excel',
            'flavorHealthUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('flavor_health/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/flavor_health_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'flavor_health_template_example.xls');
        return $response;
       
    }
}