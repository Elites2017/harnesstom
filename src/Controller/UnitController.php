<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Form\UnitUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\UnitRepository;
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
 * @Route("/unit", name="unit_")
 */
class UnitController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UnitRepository $unitRepo): Response
    {
        $units =  $unitRepo->findAll();
        $context = [
            'title' => 'Units',
            'units' => $units
        ];
        return $this->render('unit/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $unit->setCreatedBy($this->getUser());
            }
            $unit->setIsActive(true);
            $unit->setCreatedAt(new \DateTime());
            $entmanager->persist($unit);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('unit_index'));
        }

        $context = [
            'title' => 'Unit Creation',
            'unitForm' => $form->createView()
        ];
        return $this->render('unit/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Unit $unitSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Unit Details',
            'unit' => $unitSelected
        ];
        return $this->render('unit/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Unit $unit, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('unit_edit', $unit);
        $form = $this->createForm(UnitUpdateType::class, $unit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($unit);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('unit_index'));
        }

        $context = [
            'title' => 'Unit Update',
            'unitForm' => $form->createView()
        ];
        return $this->render('unit/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Unit $unit, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($unit->getId()) {
            $unit->setIsActive(!$unit->getIsActive());
        }
        $entmanager->persist($unit);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $unit->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('unit_home'));
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
            $repounit = $entmanager->getRepository(Unit::class);
            // Query how many rows are there in the unit table
            $totalUnitBefore = $repounit->createQueryBuilder('tab')
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
                    $existingUnit = $entmanager->getRepository(Unit::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingUnit) {
                        $unit = new unit();
                        if ($this->getUser()) {
                            $unit->setCreatedBy($this->getUser());
                        }
                        $unit->setOntologyId($ontology_id);
                        $unit->setName($name);
                        if ($description != null) {
                            $unit->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $unit->setParOnt($parentTermString);
                        }
                        $unit->setIsActive(true);
                        $unit->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($unit);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(Unit::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\Unit)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(Unit::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE unit SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            $totalUnitAfter = $repounit->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalUnitBefore == 0) {
                $this->addFlash('success', $totalUnitAfter . " units have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalUnitAfter - $totalUnitBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new unit has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " unit has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " units have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('unit_index'));
        }

        $context = [
            'title' => 'Unit Upload From Excel',
            'unitUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('unit/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/unit_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'unit_template_example.xls');
        return $response;
       
    }
}
