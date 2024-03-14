<?php

namespace App\Controller;

use App\Entity\GenotypingPlatform;
use App\Entity\SequencingInstrument;
use App\Entity\SequencingType;
use App\Entity\VarCallSoftware;
use App\Form\GenotypingPlatformType;
use App\Form\GenotypingPlatformUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GenotypingPlatformRepository;
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
 * @Route("/genotyping/platform", name="genotyping_platform_")
 */
class GenotypingPlatformController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GenotypingPlatformRepository $genotypingPlatformRepo): Response
    {
        $genotypingPlatforms =  $genotypingPlatformRepo->findAll();
        $context = [
            'title' => 'Genotyping Platform List',
            'genotypingPlatforms' => $genotypingPlatforms
        ];
        return $this->render('genotyping_platform/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $genotypingPlatform = new GenotypingPlatform();
        $form = $this->createForm(GenotypingPlatformType::class, $genotypingPlatform);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $genotypingPlatform->setCreatedBy($this->getUser());
            }
            $genotypingPlatform->setIsActive(true);
            $genotypingPlatform->setCreatedAt(new \DateTime());
            $entmanager->persist($genotypingPlatform);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('genotyping_platform_index'));
        }

        $context = [
            'title' => 'Genotyping Platform Creation',
            'genotypingPlatformForm' => $form->createView()
        ];
        return $this->render('genotyping_platform/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GenotypingPlatform $genotypingPlatformSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Genotyping Platform Details',
            'genotypingPlatform' => $genotypingPlatformSelected
        ];
        return $this->render('genotyping_platform/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GenotypingPlatform $genotypingPlatform, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('genotyping_platform_edit', $genotypingPlatform);
        $form = $this->createForm(GenotypingPlatformUpdateType::class, $genotypingPlatform);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($genotypingPlatform);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('genotyping_platform_index'));
        }

        $context = [
            'title' => 'Genotyping Platform Update',
            'genotypingPlatformForm' => $form->createView()
        ];
        return $this->render('genotyping_platform/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GenotypingPlatform $genotypingPlatform, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($genotypingPlatform->getId()) {
            $genotypingPlatform->setIsActive(!$genotypingPlatform->getIsActive());
        }
        $entmanager->persist($genotypingPlatform);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $genotypingPlatform->getIsActive()
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
            $repoGenotypingPlatform = $entmanager->getRepository(GenotypingPlatform::class);
            // Query how many rows are there in the GenotypingPlatform table
            $totalGenotypingPlatformBefore = $repoGenotypingPlatform->createQueryBuilder('tab')
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
                $name = $row['A'];
                $description = $row['B'];
                $ontIdSeqType = $row['C'];
                $methodDescription = $row['D'];
                $ontIdSeqIns = $row['E'];
                $ontIdVarCallSoft = $row['F'];
                $refSetName = $row['G'];
                $publishedDate = $row['H'];
                $assemblyPUI = $row['I'];
                $markerCount = $row['J'];
                $bioProjectId = $row['K'];
                $publicationRef = $row['L'];
                // check if the file doesn't have empty columns
                if ($name != null) {
                    // check if the data is upload in the database
                    $existingGenotypingPlatform = $entmanager->getRepository(GenotypingPlatform::class)->findOneBy(['name' => $name]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingGenotypingPlatform) {
                        $genotypingPlatform = new GenotypingPlatform();
                        $genotypingPlatformSeqType = $entmanager->getRepository(SequencingType::class)->findOneBy(['ontology_id' => $ontIdSeqType]);
                        if (($genotypingPlatformSeqType != null) && ($genotypingPlatformSeqType instanceof \App\Entity\SequencingType)) {
                            $genotypingPlatform->setSequencingType($genotypingPlatformSeqType);
                        }

                        $genotypingPlatformSeqIns = $entmanager->getRepository(SequencingInstrument::class)->findOneBy(['ontology_id' => $ontIdSeqIns]);
                        if (($genotypingPlatformSeqIns != null) && ($genotypingPlatformSeqIns instanceof \App\Entity\SequencingInstrument)) {
                            $genotypingPlatform->setSequencingInstrument($genotypingPlatformSeqIns);
                        }
                        
                        $genotypingPlatformVarCallSoft = $entmanager->getRepository(VarCallSoftware::class)->findOneBy(['ontology_id' => $ontIdVarCallSoft]);
                        if (($genotypingPlatformVarCallSoft != null) && ($genotypingPlatformVarCallSoft instanceof \App\Entity\VarCallSoftware)) {
                            $genotypingPlatform->setVarCallSoftware($genotypingPlatformVarCallSoft);
                        }
                        
                        if ($this->getUser()) {
                            $genotypingPlatform->setCreatedBy($this->getUser());
                        }
                        
                        $publicationRef = explode("|", $publicationRef);

                        $genotypingPlatform->setDescription($description);
                        $genotypingPlatform->setName($name);
                        $genotypingPlatform->setMarkerCount($markerCount);
                        $genotypingPlatform->setAssemblyPUI($assemblyPUI);
                        $genotypingPlatform->setBioProjectID($bioProjectId);
                        if ($publishedDate) {
                            $genotypingPlatform->setPublishedDate(\DateTime::createFromFormat('Y-m-d', $publishedDate));
                        }
                        $genotypingPlatform->setRefSetName($refSetName);
                        $genotypingPlatform->setMethodDescription($methodDescription);
                        $genotypingPlatform->setPublicationRef($publicationRef);
                        $genotypingPlatform->setIsActive(true);
                        $genotypingPlatform->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($genotypingPlatform);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalGenotypingPlatformAfter = $repoGenotypingPlatform->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGenotypingPlatformBefore == 0) {
                $this->addFlash('success', $totalGenotypingPlatformAfter . " Genotyping Platforms have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGenotypingPlatformAfter - $totalGenotypingPlatformBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Genotyping Platform has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Genotyping Platform has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Genotyping Platforms have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('genotyping_platform_index'));
        }

        $context = [
            'title' => 'Genotyping Platform Upload From Excel',
            'genotypingPlatformUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('genotyping_platform/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/genotyping_platform_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'genotyping_platform_template_example.xls');
        return $response;
       
    }
}
