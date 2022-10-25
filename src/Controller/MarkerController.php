<?php

namespace App\Controller;

use App\Entity\GenotypingPlatform;
use App\Entity\Marker;
use App\Form\MarkerType;
use App\Form\MarkerUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MarkerRepository;
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
 * @Route("/marker", name="marker_")
 */
class MarkerController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MarkerRepository $markerRepo): Response
    {
        $markers =  $markerRepo->findAll();
        $context = [
            'title' => 'Marker List',
            'markers' => $markers
        ];
        return $this->render('marker/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $marker = new Marker();
        $form = $this->createForm(MarkerType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $marker->setCreatedBy($this->getUser());
            }
            $marker->setIsActive(true);
            $marker->setCreatedAt(new \DateTime());
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Creation',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Marker $markerSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Marker Details',
            'marker' => $markerSelected
        ];
        return $this->render('marker/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('marker_edit', $marker);
        $form = $this->createForm(MarkerUpdateType::class, $marker);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($marker);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Update',
            'markerForm' => $form->createView()
        ];
        return $this->render('marker/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Marker $marker, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($marker->getId()) {
            $marker->setIsActive(!$marker->getIsActive());
        }
        $entmanager->persist($marker);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $marker->getIsActive()
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
            $repoMarker = $entmanager->getRepository(Marker::class);
            // Query how many rows are there in the Marker table
            $totalMarkerBefore = $repoMarker->createQueryBuilder('tab')
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
                $genoPlatformName = $row['A'];
                $type = $row['B'];
                $markerName = $row['C'];
                $linkageGroupName = $row['D'];
                $position = $row['E'];
                $start = $row['F'];
                $end = $row['G'];
                $refAllele = $row['H'];
                $altAllele = $row['I'];
                $primerName1 = $row['J'];
                $primerSeq1 = $row['K'];
                $primerName2 = $row['L'];
                $primerSeq2 = $row['M'];
                // check if the file doesn't have empty columns
                if ($genoPlatformName != null & $markerName != null) {
                    // check if the data is upload in the database
                    $existingMarker = $entmanager->getRepository(Marker::class)->findOneBy(['name' => $markerName, 'platformNameBuffer' => $genoPlatformName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingMarker) {
                        $marker = new Marker();
                        $markerGenoPlatform = $entmanager->getRepository(GenotypingPlatform::class)->findOneBy(['name' => $genoPlatformName]);
                        if (($markerGenoPlatform != null) && ($markerGenoPlatform instanceof \App\Entity\GenotypingPlatform)) {
                            $marker->setGenotypingPlatform($markerGenoPlatform);
                            $marker->setPlatformNameBuffer($genoPlatformName);
                        }
                        
                        if ($this->getUser()) {
                            $marker->setCreatedBy($this->getUser());
                        }
                        
                        $altAllele = explode(",", $altAllele);

                        $marker->setType($type);
                        $marker->setLinkageGroupName($linkageGroupName);
                        $marker->setPosition($position);
                        $marker->setStart($start);
                        $marker->setName($markerName);
                        $marker->setEnd($end);
                        $marker->setRefAllele($refAllele);
                        $marker->setAltAllele($altAllele);
                        $marker->setPrimerName1($primerName1);
                        $marker->setPrimerSeq1($primerSeq1);
                        $marker->setPrimerName2($primerName2);
                        $marker->setPrimerSeq2($primerSeq2);
                        $marker->setIsActive(true);
                        $marker->setCreatedAt(new \DateTime());
                        $entmanager->persist($marker);
                        $entmanager->flush();
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalMarkerAfter = $repoMarker->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMarkerBefore == 0) {
                $this->addFlash('success', $totalMarkerAfter . " Markers have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMarkerAfter - $totalMarkerBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Marker has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Marker has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Markers have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('marker_index'));
        }

        $context = [
            'title' => 'Marker Upload From Excel',
            'markerUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('marker/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/marker_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'marker_template_example.xlsx');
        return $response;
       
    }
}


