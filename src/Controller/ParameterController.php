<?php

namespace App\Controller;

use App\Entity\FactorType;
use App\Entity\Parameter;
use App\Entity\Study;
use App\Entity\StudyParameterValue;
use App\Entity\Unit;
use App\Form\ParameterType;
use App\Form\ParameterUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ParameterRepository;
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
 * @Route("/parameter", name="parameter_")
 */
class ParameterController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ParameterRepository $parameterRepo): Response
    {
        $parameters =  $parameterRepo->findAll();
        $context = [
            'title' => 'Parameter List',
            'parameters' => $parameters
        ];
        return $this->render('parameter/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $parameter = new Parameter();
        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $parameter->setCreatedBy($this->getUser());
            }
            $parameter->setIsActive(true);
            $parameter->setCreatedAt(new \DateTime());
            $entmanager->persist($parameter);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('parameter_index'));
        }

        $context = [
            'title' => 'Parameter Creation',
            'parameterForm' => $form->createView()
        ];
        return $this->render('parameter/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Parameter $parameterSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Parameter Details',
            'parameter' => $parameterSelected
        ];
        return $this->render('parameter/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Parameter $parameter, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('parameter_edit', $parameter);
        $form = $this->createForm(ParameterUpdateType::class, $parameter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($parameter);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('parameter_index'));
        }

        $context = [
            'title' => 'Parameter Update',
            'parameterForm' => $form->createView()
        ];
        return $this->render('parameter/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Parameter $parameter, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($parameter->getId()) {
            $parameter->setIsActive(!$parameter->getIsActive());
        }
        $entmanager->persist($parameter);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $parameter->getIsActive()
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
            $repoParameter = $entmanager->getRepository(Parameter::class);
            // Query how many rows are there in the Parameter table
            $totalParameterBefore = $repoParameter->createQueryBuilder('tab')
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
                $studyAbbreviation = $row['A'];
                $parameterName = $row['B'];
                $factorTypeOntId = $row['C'];
                $unitOntId = $row['D'];
                $studyParamVal = $row['E'];
                // check if the file doesn't have empty columns
                if ($parameterName != null) {
                    // check if the data is upload in the database
                    $existingParameter = $entmanager->getRepository(Parameter::class)->findOneBy(['name' => $parameterName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingParameter) {
                        $parameter = new Parameter();
                        if ($this->getUser()) {
                            $parameter->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $parameterFactorType = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $factorTypeOntId]);
                            if (($parameterFactorType != null) && ($parameterFactorType instanceof \App\Entity\FactorType)) {
                                $parameter->setFactorType($parameterFactorType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the factor ontology " .$factorTypeOntId);
                        }

                        try {
                            //code...
                            $parameterUnitOntId = $entmanager->getRepository(Unit::class)->findOneBy(['ontology_id' => $unitOntId]);
                            if (($parameterUnitOntId != null) && ($parameterUnitOntId instanceof \App\Entity\Unit)) {
                                $parameter->setUnit($parameterUnitOntId);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the unit ontology Id " .$unitOntId);
                        }

                        try {
                            //code...
                            $parameter->setName($parameterName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the parameter name " .$parameterName);
                        }

                        $parameter->setIsActive(true);
                        $parameter->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($parameter);
                            $entmanager->flush();

                            try {
                                //code...
                                $parameterStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                                if (($parameterStudy != null) && ($parameterStudy instanceof \App\Entity\Study)) {
                                    // create new study paramter value only if the study exists, because we already have the parameter
                                    $studyParameterVal = new StudyParameterValue();
                                    $studyParameterVal->setParameter($parameter);
                                    $studyParameterVal->setStudy($parameterStudy);
                                    $studyParameterVal->setValue(floatval($studyParamVal));
                                    $studyParameterVal->setIsActive(true);
                                    $studyParameterVal->setCreatedAt(new \DateTime());
                                    $studyParameterVal->setCreatedBy($this->getUser());
                                    $entmanager->persist($studyParameterVal);
                                    $entmanager->flush();
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', "A problem happened in the study parameter value, we can not save study parameter value data now due to: " .strtoupper($th->getMessage()));
                            }
                        
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalParameterAfter = $repoParameter->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalParameterBefore == 0) {
                $this->addFlash('success', $totalParameterAfter . " parameters have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalParameterAfter - $totalParameterBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new parameter has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " parameter has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " parameters have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('parameter_index'));
        }

        $context = [
            'title' => 'Parameter Upload From Excel',
            'parameterUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('parameter/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/parameter_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'parameter_template_example.xlsx');
        return $response;
       
    }
}
