<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\Germplasm;
use App\Entity\Institute;
use App\Entity\Program;
use App\Form\GermplasmType;
use App\Form\GermplasmUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GermplasmRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/germplasm", name="germplasm_")
 */
class GermplasmController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GermplasmRepository $germplasmRepo): Response
    {
        $germplasms =  $germplasmRepo->findAll();
        $context = [
            'title' => 'Germplasm List',
            'germplasms' => $germplasms
        ];
        return $this->render('germplasm/index.html.twig', $context);
    }


    public function findAccessionMaintainerNumb($numb = 1){
        $accessionRepo = $this->getDoctrine()->getRepository(Accession::class);
        $acc = $accessionRepo->findBy(['instcode' => $numb]);
        return $acc;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $germplasm = new Germplasm();
        $form = $this->createForm(GermplasmType::class, $germplasm);
        //dd($this->findAccessionMaintainerNumb(1));

        //$accessionRepo = $this->getDoctrine()->getRepository(Accession::class);
        //$acc = $accessionRepo->findBy(['instcode' => 1]);
        //dd($acc);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                    $germplasm->setCreatedBy($this->getUser());
                }
            $germplasm->setInstcode($form->get('maintainerInstituteCode')->getData());    
            $germplasm->setMaintainerNumb($form->get('accession')->getData()->getMaintainerNumb());
            $germplasm->setAccession($form->get('accession')->getData());
            $germplasm->setIsActive(true);
            $germplasm->setCreatedAt(new \DateTime());
            $entmanager->persist($germplasm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Creation',
            'acc' => $this->findAccessionMaintainerNumb(1),
            'germplasmForm' => $form->createView()
        ];
        return $this->render('germplasm/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Germplasm $germplasmselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Germplasm Details',
            'germplasm' => $germplasmselected
        ];
        return $this->render('germplasm/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Germplasm $germplasm, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('germplasm_edit', $germplasm);
        $form = $this->createForm(GermplasmUpdateType::class, $germplasm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $germplasm->setInstcode($form->get('maintainerInstituteCode')->getData());    
            $germplasm->setMaintainerNumb($form->get('accession')->getData()->getMaintainerNumb());
            $germplasm->setAccession($form->get('accession')->getData());
            $entmanager->persist($germplasm);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Update',
            'germplasmForm' => $form->createView()
        ];
        return $this->render('germplasm/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Germplasm $germplasm, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($germplasm->getId()) {
            $germplasm->setIsActive(!$germplasm->getIsActive());
        }
        $entmanager->persist($germplasm);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $germplasm->getIsActive()
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
            $repoGermplasm = $entmanager->getRepository(Germplasm::class);
            // Query how many rows are there in the Germplasm table
            $totalGermplasmBefore = $repoGermplasm->createQueryBuilder('tab')
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
                $programAbbreviation = $row['A'];
                $germplasmID = $row['B'];
                $instcode = $row['C'];
                $providerAccessionNumber = $row['D'];
                $preprocessing = $row['E'];
                // check if the file doesn't have empty columns
                if ($programAbbreviation != null && $germplasmID != null && $instcode) {
                    // check if the data is upload in the database
                    $existingGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmID]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingGermplasm) {
                        $germplasm = new Germplasm();
                        if ($this->getUser()) {
                            $germplasm->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $germplasmProgram = $entmanager->getRepository(Program::class)->findOneBy(['abbreviation' => $programAbbreviation]);
                            if (($germplasmProgram != null) && ($germplasmProgram instanceof \App\Entity\Program)) {
                                $germplasm->setProgram($germplasmProgram);
                            }
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the program " .$programAbbreviation);
                        }
                        
                        try {
                            //code...
                            $germplasmInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instcode]);
                            if (($germplasmInstitute != null) && ($germplasmInstitute instanceof \App\Entity\Institute)) {
                                $germplasm->setInstcode($germplasmInstitute);
                            }
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the institute  " .$instcode);
                        }

                        try {
                            //code...
                            $germplasm->setMaintainerInstituteCode($germplasmInstitute);
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the institute  " .$instcode);
                        }

                        try {
                            //code...
                            $germplasmAccession = $entmanager->getRepository(Accession::class)->findOneBy(['maintainernumb' => $providerAccessionNumber]);
                            if (($germplasmAccession != null) && ($germplasmAccession instanceof \App\Entity\Accession)) {
                                $germplasm->setAccession($germplasmAccession);
                            }
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession " .$providerAccessionNumber);
                        }
                        
                        try {
                            //code...
                            $germplasm->setMaintainerNumb($providerAccessionNumber);
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the accession maintainer numb " .$providerAccessionNumber);
                        }

                        try {
                            //code...
                            $germplasm->setPreprocessing($preprocessing);
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the germplasm preprocessing " .$preprocessing);
                        }

                        try {
                            //code...
                            $germplasm->setGermplasmID($germplasmID);
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the germplasm ID " .$germplasmID);
                        }
                        
                        $germplasm->setIsActive(true);
                        $germplasm->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($germplasm);
                            $entmanager->flush();
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " Can not save your data due to " .$th->getMessage());
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalGermplasmAfter = $repoGermplasm->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGermplasmBefore == 0) {
                $this->addFlash('success', $totalGermplasmAfter . " germplasms have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGermplasmAfter - $totalGermplasmBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new germplasm has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " germplasm has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " germplasms have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Upload From Excel',
            'germplasmUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('germplasm/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/germplasm_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'germplasm_template_example.xlsx');
        return $response;
       
    }
}
