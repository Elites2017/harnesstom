<?php

namespace App\Controller;

use App\Entity\Synonym;
use App\Form\SynonymType;
use App\Form\SynonymUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SynonymRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/synonym", name="synonym_")
 */
class SynonymController extends AbstractController
{
    // Add a global variable for the spreadsheet reading problem
    // due to wrong format or applied filter
    private $spreadsheet;

    function __construct(){
        $this->spreadsheet = "";
    }
    
    /**
     * @Route("/", name="index")
     */
    public function index(SynonymRepository $synonymRepo): Response
    {
        $synonyms =  $synonymRepo->findAll();
        $context = [
            'title' => 'Synonym List',
            'synonyms' => $synonyms
        ];
        return $this->render('synonym/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $synonym = new Synonym();
        $form = $this->createForm(SynonymType::class, $synonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('accession')->getData() instanceof \App\Entity\Accession) {
                $this->addFlash('danger', "You must choose an accession from the liste");
            } else {
                if ($this->getUser()) {
                    $synonym->setCreatedBy($this->getUser());
                }
                $synonym->setIsActive(true);
                $synonym->setCreatedAt(new \DateTime());
                $entmanager->persist($synonym);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('synonym_index'));
            }
        }

        $context = [
            'title' => 'Synonym Creation',
            'synonymForm' => $form->createView()
        ];
        return $this->render('synonym/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Synonym $synonymselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Synonym Details',
            'synonym' => $synonymselected
        ];
        return $this->render('synonym/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Synonym $synonym, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('synonym_edit', $synonym);
        $form = $this->createForm(SynonymUpdateType::class, $synonym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->get('accession')->getData() instanceof \App\Entity\Accession) {
                $this->addFlash('danger', "You must choose an accession from the list");
            } else {
                $entmanager->persist($synonym);
                $entmanager->flush();
                return $this->redirect($this->generateUrl('synonym_index'));
            }
        }

        $context = [
            'title' => 'Synonym Update',
            'synonymForm' => $form->createView()
        ];
        return $this->render('synonym/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Synonym $synonym, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($synonym->getId()) {
            $synonym->setIsActive(!$synonym->getIsActive());
        }
        $entmanager->persist($synonym);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $synonym->getIsActive()
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
            $repoSynonym = $entmanager->getRepository(Synonym::class);
            // Query how many rows are there in the Synonym table
            $totalSynonymBefore = $repoSynonym->createQueryBuilder('tab')
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
            
            try {
                //code...
                // read from the uploaded file
                $spreadsheet = IOFactory::load($fileFolder . $filePathName);
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', "Error in the file data format or applied filter, try to clean your data and try again");
            }
            
            // remove the first row (title) of the file
            $spreadsheet->getActiveSheet()->removeRow(1);
            // transform the uploaded file to an array
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            // loop over the array to get each row
            foreach ($sheetData as $key => $row) {
                $programAbbreviation = $row['A'];
                $synonymAbbreviation = $row['B'];
                $synonymName = $row['C'];
                $synonymDescription = $row['D'];
                $ontIdSynonymType = $row['E'];
                $synonymPUI = $row['F'];
                $startDate = $row['G'];
                $endDate = $row['H'];
                $publicReleaseDate = $row['I'];
                $licence = $row['J'];
                $publicationRef = $row['K'];
                // check if the file doesn't have empty columns
                if ($synonymAbbreviation != null && $synonymName != null) {
                    // check if the data is upload in the database
                    $existingSynonym = $entmanager->getRepository(Synonym::class)->findOneBy(['abbreviation' => $synonymAbbreviation]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingSynonym) {
                        $synonym = new Synonym();
                        if ($this->getUser()) {
                            $synonym->setCreatedBy($this->getUser());
                        }
                        $synonymProgram = $entmanager->getRepository(Program::class)->findOneBy(['abbreviation' => $programAbbreviation]);
                        if (($synonymProgram != null) && ($synonymProgram instanceof \App\Entity\Program)) {
                            $synonym->setProgram($synonymProgram);
                        }
                        $synonymType = $entmanager->getRepository(EntitySynonymType::class)->findOneBy(['ontology_id' => $ontIdSynonymType]);
                        if (($synonymType != null) && ($synonymType instanceof \App\Entity\SynonymType)) {
                            $synonym->setSynonymType($synonymType);
                        }
                        $synonym->setDescription($synonymDescription);
                        if ($startDate !=null) {
                            $synonym->setStartDate(\DateTime::createFromFormat('Y-m-d', $startDate));
                        }
                        if ($endDate !=null) {
                            $synonym->setEndDate(\DateTime::createFromFormat('Y-m-d', $endDate));
                        }
                        if ($publicReleaseDate !=null) {
                            $synonym->setPublicReleaseDate(\DateTime::createFromFormat('Y-m-d', $publicReleaseDate));
                        }
                        $synonym->setName($synonymName);
                        $synonym->setPui($synonymPUI);
                        $synonym->setAbbreviation($synonymAbbreviation);
                        $publicationRef = explode("|", $publicationRef);
                        $synonym->setPublicationReference($publicationRef);
                        $synonym->setLicense($licence);
                        $synonym->setIsActive(true);
                        $synonym->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($synonym);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // Query how many rows are there in the table
            $totalSynonymAfter = $repoSynonym->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSynonymBefore == 0) {
                $this->addFlash('success', $totalSynonymAfter . " synonyms have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSynonymAfter - $totalSynonymBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new synonym has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " synonym has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " synonyms have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('synonym_index'));
        }

        $context = [
            'title' => 'Synonym Upload From Excel',
            'synonymUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('synonym/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/synonym_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'synonym_template_example.xlsx');
        return $response;
       
    }
}
