<?php

namespace App\Controller;

use App\Entity\DataType;
use App\Form\DataCreateType;
use App\Form\DataUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\DataTypeRepository;
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
 * @Route("/data/type", name="data_type_")
 */
class DataTypeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DataTypeRepository $dataTypeRepo): Response
    {
        $dataTypes =  $dataTypeRepo->findAll();
        $context = [
            'title' => 'Data Type',
            'dataTypes' => $dataTypes
        ];
        return $this->render('data_type/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $dataType = new DataType();
        $form = $this->createForm(DataCreateType::class, $dataType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $dataType->setCreatedBy($this->getUser());
            }
            $dataType->setIsActive(true);
            $dataType->setCreatedAt(new \DateTime());
            $entmanager->persist($dataType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('data_type_index'));
        }

        $context = [
            'title' => 'Data Type Creation',
            'dataTypeForm' => $form->createView()
        ];
        return $this->render('data_type/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(DataType $dataTypeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Data Type Details',
            'dataType' => $dataTypeSelected
        ];
        return $this->render('data_type/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(DataType $dataType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('data_type_edit', $dataType);
        $form = $this->createForm(DataUpdateType::class, $dataType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($dataType);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('data_type_index'));
        }

        $context = [
            'title' => 'Data Type Update',
            'dataTypeForm' => $form->createView()
        ];
        return $this->render('data_type/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(DataType $dataType, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($dataType->getId()) {
            $dataType->setIsActive(!$dataType->getIsActive());
        }
        $entmanager->persist($dataType);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $dataType->getIsActive()
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
            $repoDataType = $entmanager->getRepository(DataType::class);
            // Query how many rows are there in the DataType table
            $totalDataTypeBefore = $repoDataType->createQueryBuilder('tab')
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
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingDataType = $entmanager->getRepository(DataType::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingDataType) {
                        $dataType = new DataType();
                        $ontologyIdParentTerm = $entmanager->getRepository(DataType::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\DataType)) {
                            $dataType->setParentTerm($ontologyIdParentTerm);
                        }
                        if ($this->getUser()) {
                            $dataType->setCreatedBy($this->getUser());
                        }
                        $dataType->setOntologyId($ontology_id);
                        $dataType->setName($name);
                        $dataType->setDescription($description);
                        $dataType->setIsActive(true);
                        $dataType->setCreatedAt(new \DateTime());
                        $entmanager->persist($dataType);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalDataTypeAfter = $repoDataType->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalDataTypeBefore == 0) {
                $this->addFlash('success', $totalDataTypeAfter . " data types have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalDataTypeAfter - $totalDataTypeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new data type has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " data type has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " data types have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('data_type_index'));
        }

        $context = [
            'title' => 'DataType Upload From Excel',
            'dataTypeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('data_type/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/data_type_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'data_type_template_example.xls');
        return $response;
       
    }
}
