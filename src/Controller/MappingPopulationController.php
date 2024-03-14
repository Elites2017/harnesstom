<?php

namespace App\Controller;

use App\Entity\Cross;
use App\Entity\Generation;
use App\Entity\MappingPopulation;
use App\Form\MappingPopulationType;
use App\Form\MappingPopulationUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MappingPopulationRepository;
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
 * @Route("/mapping/population", name="mapping_population_")
 */
class MappingPopulationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MappingPopulationRepository $mappingPopulationRepo): Response
    {
        $mappingPopulations = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $mappingPopulations = $mappingPopulationRepo->findAll();
            } else {
                $mappingPopulations = $mappingPopulationRepo->findReleasedTrialStudyCrossMappingPop($this->getUser());
            }
        } else {
            $mappingPopulations = $mappingPopulationRepo->findReleasedTrialStudyCrossMappingPop();
        }
        $context = [
            'title' => 'Mapping Population List',
            'mappingPopulations' => $mappingPopulations
        ];
        return $this->render('mapping_population/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mappingPopulation = new MappingPopulation();
        $form = $this->createForm(MappingPopulationType::class, $mappingPopulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $mappingPopulation->setCreatedBy($this->getUser());
            }
            $mappingPopulation->setIsActive(true);
            $mappingPopulation->setCreatedAt(new \DateTime());
            $entmanager->persist($mappingPopulation);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('mapping_population_index'));
        }

        $context = [
            'title' => 'Mapping Population Creation',
            'mappingPopulationForm' => $form->createView()
        ];
        return $this->render('mapping_population/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MappingPopulation $mappingPopulationSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Mapping Population Details',
            'mappingPopulation' => $mappingPopulationSelected
        ];
        return $this->render('mapping_population/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MappingPopulation $mappingPopulation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('mapping_population_edit', $mappingPopulation);
        $form = $this->createForm(MappingPopulationUpdateType::class, $mappingPopulation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($mappingPopulation);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('mapping_population_index'));
        }

        $context = [
            'title' => 'Mapping Population Update',
            'mappingPopulationForm' => $form->createView()
        ];
        return $this->render('mapping_population/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MappingPopulation $mappingPopulation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($mappingPopulation->getId()) {
            $mappingPopulation->setIsActive(!$mappingPopulation->getIsActive());
        }
        $entmanager->persist($mappingPopulation);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $mappingPopulation->getIsActive()
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
            $repoMappingPopulation = $entmanager->getRepository(MappingPopulation::class);
            // Query how many rows are there in the MappingPopulation table
            $totalMappingPopBefore = $repoMappingPopulation->createQueryBuilder('tab')
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
                $mappingPopName = $row['A'];
                $crossName= $row['B'];
                $generationOntId = $row['C'];
                $publicationRef = $row['D'];
                // check if the file doesn't have empty columns
                if ($mappingPopName != null && $crossName != null && $generationOntId != null) {
                    // check if the data is upload in the database
                    $existingMappingPopulation = $entmanager->getRepository(MappingPopulation::class)->findOneBy(['name' => $mappingPopName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingMappingPopulation) {
                        $mappingPopulation = new MappingPopulation();
                        if ($this->getUser()) {
                            $mappingPopulation->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $mappingPopCross = $entmanager->getRepository(Cross::class)->findOneBy(['name' => $crossName]);
                            if (($mappingPopCross != null) && ($mappingPopCross instanceof \App\Entity\Cross)) {
                                $mappingPopulation->setMappingPopulationCross($mappingPopCross);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the cross name " .$crossName);
                        }

                        try {
                            //code...
                            $mappingPopGeneration = $entmanager->getRepository(Generation::class)->findOneBy(['ontology_id' => $generationOntId]);
                            if (($mappingPopGeneration != null) && ($mappingPopGeneration instanceof \App\Entity\Generation)) {
                                $mappingPopulation->setPedigreeGeneration($mappingPopGeneration);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the generation ontology " .$generationOntId);
                        }

                        try {
                            //code...
                            $mappingPopulation->setName($mappingPopName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the name " .$mappingPopName);
                        }

                        $publicationRef = explode(";", $publicationRef);
                        
                        try {
                            //code...
                            $mappingPopulation->setPublicationRef($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the publication Reference " .$publicationRef);
                        }

                        $mappingPopulation->setIsActive(true);
                        $mappingPopulation->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($mappingPopulation);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalMappingPopAfter = $repoMappingPopulation->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMappingPopBefore == 0) {
                $this->addFlash('success', $totalMappingPopAfter . " mapping populations have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMappingPopAfter - $totalMappingPopBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new mapping population has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " mapping population has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " mapping populations have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('mapping_population_index'));
        }

        $context = [
            'title' => 'Mapping Population Upload From Excel',
            'mappingPopulationUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('mapping_population/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/mapping_population_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'mapping_population_template_example.xlsx');
        return $response;
       
    }
}
