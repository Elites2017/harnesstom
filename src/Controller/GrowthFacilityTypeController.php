<?php

namespace App\Controller;

use App\Entity\GrowthFacilityType;
use App\Form\GrowthFacilityCreateType;
use App\Form\GrowthFacilityUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GrowthFacilityTypeRepository;
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
 * @Route("growth/facility/type", name="growth_facility_type_")
 */
class GrowthFacilityTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GrowthFacilityTypeRepository $growthFacilityTypeRepo): Response
    {
        $growthFacilityTypes =  $growthFacilityTypeRepo->findAll();
        $parentsOnly = $growthFacilityTypeRepo->getParentsOnly();
        $context = [
            'title' => 'Growth Facility Type List',
            'growthFacilityTypes' => $growthFacilityTypes,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('growth_facility_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $growthFacilityType = new GrowthFacilityType();
        $form = $this->createForm(GrowthFacilityCreateType::class, $growthFacilityType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $growthFacilityType->setCreatedBy($this->getUser());
            }
            $growthFacilityType->setIsActive(true);
            $growthFacilityType->setCreatedAt(new \DateTime());
            $entmanager->persist($growthFacilityType);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('growth_facility_type_index'));
        }

        $context = [
            'title' => 'Growth Facility Type Creation',
            'growthFacilityTypeForm' => $form->createView()
        ];
        return $this->render('growth_facility_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GrowthFacilityType $growthFacilityTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Growth Facility Type Details',
            'growthFacilityType' => $growthFacilityTypeSelected
        ];
        return $this->render('growth_facility_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GrowthFacilityType $growthFacilityType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('growth_facility_ype_edit', $growthFacilityType);
        $form = $this->createForm(GrowthFacilityUpdateType::class, $growthFacilityType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($growthFacilityType);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('growth_facility_type_index'));
        }

        $context = [
            'title' => 'Growth Facility Type Update',
            'growthFacilityTypeForm' => $form->createView()
        ];
        return $this->render('growth_facility_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GrowthFacilityType $growthFacilityType, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($growthFacilityType->getId()) {
            $growthFacilityType->setIsActive(!$growthFacilityType->getIsActive());
        }
        $entmanager->persist($growthFacilityType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $growthFacilityType->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('growthFacility_home'));
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
            $repoGrowthFacilityType = $entmanager->getRepository(GrowthFacilityType::class);
            // Query how many rows are there in the GrowthFacilityType table
            $totalGrowthFacilityTypeBefore = $repoGrowthFacilityType->createQueryBuilder('tab')
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
                    $existingGrowthFacilityType = $entmanager->getRepository(GrowthFacilityType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingGrowthFacilityType) {
                        $growthFacilityType = new GrowthFacilityType();
                        if ($this->getUser()) {
                            $growthFacilityType->setCreatedBy($this->getUser());
                        }
                        $growthFacilityType->setOntologyId($ontology_id);
                        $growthFacilityType->setName($name);
                        if ($description != null) {
                            $growthFacilityType->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $growthFacilityType->setParOnt($parentTermString);
                        }
                        $growthFacilityType->setIsActive(true);
                        $growthFacilityType->setCreatedAt(new \DateTime());
                       
                        try {
                            //code...
                            $entmanager->persist($growthFacilityType);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(GrowthFacilityType::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\GrowthFacilityType)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(GrowthFacilityType::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE growth_facility_type SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalGrowthFacilityTypeAfter = $repoGrowthFacilityType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGrowthFacilityTypeBefore == 0) {
                $this->addFlash('success', $totalGrowthFacilityTypeAfter . " growth facility types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGrowthFacilityTypeAfter - $totalGrowthFacilityTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new growth facility type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " growth facility type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " growth facility types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('growth_facility_type_index'));
        }

        $context = [
            'title' => 'Growth Facility Type Upload From Excel',
            'growthFacilityTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('growth_facility_type/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/growth_facility_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'growth_facility_type_template_example.xls');
        return $response;
       
    }
}