<?php

namespace App\Controller;

use App\Entity\MethodClass;
use App\Entity\ObservationVariableMethod;
use App\Form\ObservationVariableMethodType;
use App\Form\ObservationVariableMethodUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ObservationVariableMethodRepository;
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
 * @Route("/observation/variable/method", name="observation_variable_method_")
 */
class ObservationVariableMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationVariableMethodRepository $observationVariableMethodRepo): Response
    {
        $observationVariableMethods =  $observationVariableMethodRepo->findAll();
        $context = [
            'title' => 'Observation Variable Method List',
            'observationVariableMethods' => $observationVariableMethods
        ];
        return $this->render('observation_variable_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationVariableMethod = new ObservationVariableMethod();
        $form = $this->createForm(ObservationVariableMethodType::class, $observationVariableMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationVariableMethod->setCreatedBy($this->getUser());
            }
            $observationVariableMethod->setIsActive(true);
            $observationVariableMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($observationVariableMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_method_index'));
        }

        $context = [
            'title' => 'Observation Variable Method Creation',
            'observationVariableMethodForm' => $form->createView()
        ];
        return $this->render('observation_variable_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationVariableMethod $observationVariableMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Variable Method Details',
            'observationVariableMethod' => $observationVariableMethodSelected
        ];
        return $this->render('observation_variable_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationVariableMethod $observationVariableMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_variable_method_edit', $observationVariableMethod);
        $form = $this->createForm(ObservationVariableMethodUpdateType::class, $observationVariableMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationVariableMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('observation_variable_method_index'));
        }

        $context = [
            'title' => 'Observation Variable Method Update',
            'observationVariableMethodForm' => $form->createView()
        ];
        return $this->render('observation_variable_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationVariableMethod $observationVariableMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationVariableMethod->getId()) {
            $observationVariableMethod->setIsActive(!$observationVariableMethod->getIsActive());
        }
        $entmanager->persist($observationVariableMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationVariableMethod->getIsActive()
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
            $repoObservationVariableMethod = $entmanager->getRepository(ObservationVariableMethod::class);
            // Query how many rows are there in the ObservationVariableMethod table
            $totalObservationVariableMethodBefore = $repoObservationVariableMethod->createQueryBuilder('tab')
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
                $obsVarMethodName = $row['A'];
                $obsVarMethodClass = $row['B'];
                $obsVarDesc = $row['D'];
                $instrument = $row['E'];
                $software = $row['F'];
                $biblioRef = $row['G'];
                //$parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($obsVarMethodName != null && $obsVarMethodClass != null) {
                    // check if the data is upload in the database
                    $existingObservationVariableMethod = $entmanager->getRepository(ObservationVariableMethod::class)->findOneBy(['name' => $obsVarMethodName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingObservationVariableMethod) {
                        $observationVariableMethod = new ObservationVariableMethod();
                        if ($this->getUser()) {
                            $observationVariableMethod->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $obsVarMethodClass = $entmanager->getRepository(MethodClass::class)->findOneBy(['ontology_id' => $obsVarMethodClass]);
                            if (($obsVarMethodClass != null) && ($obsVarMethodClass instanceof \App\Entity\MethodClass)) {
                                $observationVariableMethod->setMethodClass($obsVarMethodClass);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the method class name " .$obsVarMethodClass);
                        }

                        try {
                            //code...
                            $observationVariableMethod->setName($obsVarMethodName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method name " .$obsVarMethodName);
                        }

                        try {
                            //code...
                            $observationVariableMethod->setDescription($obsVarDesc);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method description " .$obsVarDesc);
                        }

                        try {
                            //code...
                            $observationVariableMethod->setInstrument($instrument);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method instrument " .$instrument);
                        }

                        try {
                            //code...
                            $observationVariableMethod->setSoftware($software);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method software " .$software);
                        }

                        try {
                            //code...
                            $biblioRef = explode(",", $biblioRef);
                            $observationVariableMethod->setPublicationReference($biblioRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method biblio ref " .$biblioRef);
                        }
                                                
                        $observationVariableMethod->setIsActive(true);
                        $observationVariableMethod->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($observationVariableMethod);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalObservationVariableMethodAfter = $repoObservationVariableMethod->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalObservationVariableMethodBefore == 0) {
                $this->addFlash('success', $totalObservationVariableMethodAfter . " observation variable methods have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalObservationVariableMethodAfter - $totalObservationVariableMethodBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new observation variable method has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation variable method has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation variable methods have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('observation_variable_method_index'));
        }

        $context = [
            'title' => 'Trait Class Upload From Excel',
            'observationVariableMethodUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('observation_variable_method/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/observation_variable_method_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'observation_variable_method_template_example.xlsx');
        return $response;
       
    }
}
