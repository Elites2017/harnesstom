<?php

namespace App\Controller;

use App\Entity\Germplasm;
use App\Entity\Study;
use App\Form\UploadFromExcelType;
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
 * @Route("/germplasm/study", name="germplasm_study_")
 */
class GermplasmStudyController extends AbstractController
{
    /**
     * @Route("/germplasm/study", name="germplasm_study_")
     */
    public function index(): Response
    {
        return $this->render('germplasm_study/index.html.twig', [
            'controller_name' => 'GermplasmStudyController',
        ]);
    }
    // this is to upload data in bulk using an excel file for germplasm x study
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function germplasmStudyUploadFromExcel(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Query how many rows are there in the Germplasm table
            // get the connection
            $connexion = $entmanager->getConnection();
            $oldVal = 0;
            $newVal = 0;
            try {
                //code...
                $result = $connexion->executeQuery("SELECT COUNT(GERMPLASM_ID) FROM germplasm_study")->fetchAllNumeric();
                if ($result[0][0]) {
                    $oldVal = $result[0][0];
                }
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', " Can not count the number of affected rows for the germplasm x study table before saving the data" .$th->getMessage());
            }

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
                $germplasmID = $row['A'];
                $studyAbbreviation = $row['B'];
                // check if the file doesn't have empty columns
                if ($studyAbbreviation != null && $germplasmID != null) {
                    // check if the data is upload in the database
                    $existingGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmID]);
                    $existingStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                    // upload data only for objects that haven't been saved in the database
                    if (($existingGermplasm) && ($existingStudy)) {
                        try {
                            //code...
                            $existingGermplasm->addStudy($existingStudy);
                            $entmanager->flush();
                        } catch (\Exception $th) {
                            //throw $th;
                            $this->addFlash('danger', " Can not save your data due to " .$th->getMessage());
                        }
                    } else {
                        if (($existingGermplasm) && (!$existingStudy)) {
                            $this->addFlash('danger', "The study whose abbreviation is " .$studyAbbreviation. " has not been not saved in the database before, only saved study can be used for this operation");
                        }
                        if ((!$existingGermplasm) && ($existingStudy)) {
                            $this->addFlash('danger', "The germplasm whose germplasmID is " .$germplasmID. " has not been not saved in the database before, only saved germplasm can be used for this operation");
                        }
                        else {
                            $this->addFlash('danger', "The germplasm " .$germplasmID. " and the study ". $studyAbbreviation. " have not been not saved in the database, only saved germplam and study can be used for this operation");
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            try {
                //code...
                $result = $connexion->executeQuery("SELECT COUNT(GERMPLASM_ID) FROM germplasm_study")->fetchAllNumeric();
                if ($result[0][0]) {
                    $newVal = $result[0][0];
                }
            } catch (\Throwable $th) {
                //throw $th;
                $this->addFlash('danger', " Can not count the number of affected rows for the germplasm x study table before saving the data" .$th->getMessage());
            }

            if ($oldVal == 0) {
                $this->addFlash('success', "Germplams x Study: " .$newVal . " rows have been successfuly affected.");
            } else {
                $diffBeforeAndAfter = $newVal - $oldVal;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "Germplams x Study: No new rows have been added / affected");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', "Germplams x Study: " .$diffBeforeAndAfter . " row has been successfuly affected");
                } else {
                    $this->addFlash('success', "Germplams x Study: " .$diffBeforeAndAfter . " rows have been successfuly affected");
                }
            }
            return $this->redirect($this->generateUrl('germplasm_index'));
        }

        $context = [
            'title' => 'Germplasm Upload From Excel',
            'germplasmStudyUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('germplasm_study/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function germplasmStudyExcelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/germplasm_study_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'germplasm_study_template_example.xlsx');
        return $response;
       
    }
}
