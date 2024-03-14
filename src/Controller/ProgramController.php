<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Crop;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Form\ProgramUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ProgramRepository;
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
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ProgramRepository $programRepo): Response
    {
        $programs =  $programRepo->findAll();
        $context = [
            'title' => 'Program List',
            'programs' => $programs
        ];
        return $this->render('program/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $program->setCreatedBy($this->getUser());
            }
            $program->setIsActive(true);
            $program->setCreatedAt(new \DateTime());
            $entmanager->persist($program);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('program_index'));
        }

        $context = [
            'title' => 'Program Creation',
            'programForm' => $form->createView()
        ];
        return $this->render('program/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Program $programSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Program Details',
            'program' => $programSelected
        ];
        return $this->render('program/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Program $program, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('program_edit', $program);
        $form = $this->createForm(ProgramUpdateType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($program);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('program_index'));
        }

        $context = [
            'title' => 'Program Update',
            'programForm' => $form->createView()
        ];
        return $this->render('program/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Program $program, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($program->getId()) {
            $program->setIsActive(!$program->getIsActive());
        }
        $entmanager->persist($program);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $program->getIsActive()
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
            $repoProgram = $entmanager->getRepository(Program::class);
            // Query how many rows are there in the Program table
            $totalProgramBefore = $repoProgram->createQueryBuilder('tab')
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
                $cropName = $row['A'];
                $programName = $row['B'];
                $progAbbreviation = $row['C'];
                $progObjective = $row['D'];
                $contactOrcid = $row['E'];
                $externalRef = $row['F'];
                // check if the file doesn't have empty columns
                if ($programName != null & $contactOrcid != null) {
                    // check if the data is upload in the database
                    $existingProgram = $entmanager->getRepository(Program::class)->findOneBy(['abbreviation' => $progAbbreviation]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingProgram) {
                        $program = new Program();
                        $programCrop = $entmanager->getRepository(Crop::class)->findOneBy(['commonCropName' => $cropName]);
                        if (($programCrop != null) && ($programCrop instanceof \App\Entity\Crop)) {
                            $program->setCrop($programCrop);
                        }
                        $programContact = $entmanager->getRepository(Contact::class)->findOneBy(['orcid' => $contactOrcid]);
                        if (($programContact != null) && ($programContact instanceof \App\Entity\Contact)) {
                            $program->setContact($programContact);
                        }
                        if ($this->getUser()) {
                            $program->setCreatedBy($this->getUser());
                        }
                        $program->setName($programName);
                        $program->setAbbreviation($progAbbreviation);
                        $program->setObjective($progObjective);
                        $program->setExternalRef($externalRef);
                        $program->setIsActive(true);
                        $program->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($program);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalProgramAfter = $repoProgram->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalProgramBefore == 0) {
                $this->addFlash('success', $totalProgramAfter . " programs have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalProgramAfter - $totalProgramBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Program has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " program has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " programs have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('program_index'));
        }

        $context = [
            'title' => 'Program Upload From Excel',
            'programUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('program/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/program_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'program_template_example.xlsx');
        return $response;
       
    }
}


