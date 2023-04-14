<?php

namespace App\Controller;

use App\Entity\MarkerSynonym;
use App\Entity\Marker;
use App\Form\MarkerSynonymType;
use App\Form\MarkerSynonymUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MarkerSynonymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/marker/synonym", name="marker_synonym_")
 */
class MarkerSynonymController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MarkerSynonymRepository $markerSynonymRepo): Response
    {
        $markerSynonyms =  $markerSynonymRepo->findAll();
        $context = [
            'title' => 'Marker Synonym List',
            'markerSynonyms' => $markerSynonyms
        ];
        return $this->render('marker_synonym/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $markerSynonym = new MarkerSynonym();
        $form = $this->createForm(MarkerSynonymType::class, $markerSynonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $markerSynonym->setCreatedBy($this->getUser());
            }
            $markerSynonym->setIsActive(true);
            $markerSynonym->setCreatedAt(new \DateTime());
            $this->addFlash('success', "A new synonym has been successfuly added");
            $entmanager->persist($markerSynonym);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_synonym_index'));
        }

        $context = [
            'title' => 'Marker Synonym Creation',
            'markerSynonymForm' => $form->createView()
        ];
        return $this->render('marker_synonym/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MarkerSynonym $markerSynonymSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Marker Synonym Details',
            'markerSynonym' => $markerSynonymSelected
        ];
        return $this->render('marker_synonym/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MarkerSynonym $markerSynonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('marker_synonym_edit', $markerSynonym);
        $form = $this->createForm(MarkerSynonymUpdateType::class, $markerSynonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($markerSynonym);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_synonym_index'));
        }

        $context = [
            'title' => 'Marker Synonym Update',
            'markerSynonymForm' => $form->createView()
        ];
        return $this->render('marker_synonym/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MarkerSynonym $markerSynonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($markerSynonym->getId()) {
            $markerSynonym->setIsActive(!$markerSynonym->getIsActive());
        }
        $entmanager->persist($markerSynonym);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $markerSynonym->getIsActive()
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
            $repoMarkerSynonym = $entmanager->getRepository(MarkerSynonym::class);
            // Query how many rows are there in the Marker table
            $totalMarkerSynonymBefore = $repoMarkerSynonym->createQueryBuilder('tab')
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
                $markerName = $row['A'];
                $synonymSource = $row['B'];
                $markerSynonymId = $row['C'];
                // check if the file doesn't have empty columns
                if ($markerName != null & $synonymSource != null && $markerSynonymId != null) {
                    // check if for the provided name, a maker exists in the DB
                    $existingMarker = $entmanager->getRepository(Marker::class)->findBy(['name' => $markerName]);
                    if ($existingMarker) {
                        foreach ($existingMarker as $key => $markerGenP) {
                            // check if the data is upload in the database
                            $existingMarkerSynonym = $entmanager->getRepository(MarkerSynonym::class)->findOneBy(['markerName' => $markerGenP->getId(), 'synonymSource' => $synonymSource, 'markerSynonymId' => $markerSynonymId]);
                            // upload data only for objects that haven't been saved in the database
                            if (!$existingMarkerSynonym) {
                                $markerSynonym = new MarkerSynonym();
                                $markerSynonym->setMarkerName($markerGenP);
                                $markerSynonym->setSynonymSource($synonymSource);
                                $markerSynonym->setMarkerSynonymId($markerSynonymId);
                                if ($this->getUser()) {
                                    $markerSynonym->setCreatedBy($this->getUser());
                                }
                                $markerSynonym->setIsActive(true);
                                $markerSynonym->setCreatedAt(new \DateTime());
                                
                                try {
                                    //code...
                                    $entmanager->persist($markerSynonym);
                                    $entmanager->flush();
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                                }
                            }
                        }
                    } else {
                        $this->addFlash('danger', "The marker name " .$markerName. " doesn't exist in our database.");
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalMarkerSynonymAfter = $repoMarkerSynonym->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMarkerSynonymBefore == 0) {
                $this->addFlash('success', $totalMarkerSynonymAfter . " Marker synonyms have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMarkerSynonymAfter - $totalMarkerSynonymBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new marker synonym has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Marker synonym has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Marker synonyms have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('marker_synonym_index'));
        }

        $context = [
            'title' => 'Marker Synonym Upload From Excel',
            'markerSynonymUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('marker_synonym/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/marker_synonym_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'marker_synonym_template_example.xlsx');
        return $response;  
    }
}