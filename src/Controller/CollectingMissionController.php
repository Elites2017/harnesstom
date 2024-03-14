<?php

namespace App\Controller;

use App\Entity\CollectingMission;
use App\Entity\Institute;
use App\Form\CollectingMissionType;
use App\Form\CollectingMissionUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\CollectingMissionRepository;
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
 * @Route("/collecting/mission", name="collecting_mission_")
 */
class CollectingMissionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CollectingMissionRepository $collectingMissionRepo): Response
    {
        $collectingMissions =  $collectingMissionRepo->findAll();
        $context = [
            'title' => 'Collecting Mission List',
            'collectingMissions' => $collectingMissions
        ];
        return $this->render('collecting_mission/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $collectingMission = new CollectingMission();
        $form = $this->createForm(CollectingMissionType::class, $collectingMission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $collectingMission->setCreatedBy($this->getUser());
            }
            $collectingMission->setIsActive(true);
            $collectingMission->setCreatedAt(new \DateTime());
            $entmanager->persist($collectingMission);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('collecting_mission_index'));
        }

        $context = [
            'title' => 'Collecting Mission Creation',
            'collectingMissionForm' => $form->createView()
        ];
        return $this->render('collecting_mission/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CollectingMission $collectingMissionSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Collecting Mission Details',
            'collectingMission' => $collectingMissionSelected
        ];
        return $this->render('collecting_mission/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CollectingMission $collectingMission, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('collecting_mission_edit', $collectingMission);
        $form = $this->createForm(CollectingMissionUpdateType::class, $collectingMission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($collectingMission);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('collecting_mission_index'));
        }

        $context = [
            'title' => 'Collecting Mission Update',
            'collectingMissionForm' => $form->createView()
        ];
        return $this->render('collecting_mission/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CollectingMission $collectingMission, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($collectingMission->getId()) {
            $collectingMission->setIsActive(!$collectingMission->getIsActive());
        }
        $entmanager->persist($collectingMission);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $collectingMission->getIsActive()
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
            $repoCollectingMission = $entmanager->getRepository(CollectingMission::class);
            // Query how many rows are there in the CollectingMission table
            $totalCollectingMissionBefore = $repoCollectingMission->createQueryBuilder('tab')
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
                $collectingMissionName = $row['A'];
                $collectingMissionInstcode = $row['B'];
                $collectingMissionSpecies = $row['C'];
                $collectingMissionDescription = $row['D'];
                // check if the file doesn't have empty columns
                if ($collectingMissionName != null) {
                    // check if the data is upload in the database
                    $existingCollectingMission = $entmanager->getRepository(CollectingMission::class)->findOneBy(['name' => $collectingMissionName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingCollectingMission) {
                        $collectingMission = new CollectingMission();
                        if ($this->getUser()) {
                            $collectingMission->setCreatedBy($this->getUser());
                        }
                        $collectingMissionInstcode = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $collectingMissionInstcode]);
                        if (($collectingMissionInstcode != null) && ($collectingMissionInstcode instanceof \App\Entity\Institute)) {
                            $collectingMission->setInstitute($collectingMissionInstcode);
                        }
                        $collectingMission->setName($collectingMissionName);
                        $collectingMission->setSpecies($collectingMissionSpecies);
                        $collectingMission->setIsActive(true);
                        $collectingMission->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($collectingMission);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // Query how many rows are there in the table
            $totalCollectingMissionAfter = $repoCollectingMission->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCollectingMissionBefore == 0) {
                $this->addFlash('success', $totalCollectingMissionAfter . " collecting missiond have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCollectingMissionAfter - $totalCollectingMissionBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new collecting mission has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " collecting mission has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " collecting missiond have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('collecting_mission_index'));
        }

        $context = [
            'title' => 'Collecting Mission Upload From Excel',
            'collectingMissionUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('collecting_mission/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/collecting_mission_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'collecting_mission_template_example.xlsx');
        return $response;
       
    }
}

