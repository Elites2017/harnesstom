<?php

namespace App\Controller;

use App\Entity\ObservationVariable;
use App\Entity\ObservationVariableMethod;
use App\Entity\Scale;
use App\Entity\TraitClass;
use App\Form\ObservationVariableType;
use App\Form\ObservationVariableUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ObservationVariableRepository;
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
 * @Route("/observation/variable", name="observation_variable_")
 */
class ObservationVariableController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ObservationVariableRepository $observationVariableRepo): Response
    {
        $observationVariables =  $observationVariableRepo->findAll();
        $context = [
            'title' => 'Observation Variable List',
            'observationVariables' => $observationVariables
        ];
        return $this->render('observation_variable/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $observationVariable = new ObservationVariable();
        $form = $this->createForm(ObservationVariableType::class, $observationVariable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $observationVariable->setCreatedBy($this->getUser());
            }
            $observationVariable->setIsActive(true);
            $observationVariable->setCreatedAt(new \DateTime());
            $entmanager->persist($observationVariable);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('observation_variable_index'));
        }

        $context = [
            'title' => 'Observation Variable Creation',
            'observationVariableForm' => $form->createView()
        ];
        return $this->render('observation_variable/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ObservationVariable $observationVariableSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Observation Variable Details',
            'observationVariable' => $observationVariableSelected
        ];
        return $this->render('observation_variable/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ObservationVariable $observationVariable, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('observation_variable_edit', $observationVariable);
        $form = $this->createForm(ObservationVariableUpdateType::class, $observationVariable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($observationVariable);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('observation_variable_index'));
        }

        $context = [
            'title' => 'Observation Variable Update',
            'observationVariableForm' => $form->createView()
        ];
        return $this->render('observation_variable/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ObservationVariable $observationVariable, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($observationVariable->getId()) {
            $observationVariable->setIsActive(!$observationVariable->getIsActive());
        }
        $entmanager->persist($observationVariable);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $observationVariable->getIsActive()
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
            $repoObservationVariable = $entmanager->getRepository(ObservationVariable::class);
            // Query how many rows are there in the ObservationVariable table
            $totalObservationVariableBefore = $repoObservationVariable->createQueryBuilder('tab')
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
                $obsVarIdTrait = $row['A'];
                $obsName = $row['B'];
                $traitOntId = $row['C'];
                $obsVarDesc = $row['E'];
                $scaleName = $row['F'];
                $obsVarMethodName = $row['G'];
                // check if the file doesn't have empty columns
                if ($obsVarIdTrait != null && $obsName != null) {
                    // check if the data is upload in the database
                    $existingObservationVariable = $entmanager->getRepository(ObservationVariable::class)->findOneBy(['name' => $obsName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingObservationVariable) {
                        $observationVariable = new ObservationVariable();
                        if ($this->getUser()) {
                            $observationVariable->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $obsVarTrait = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $traitOntId]);
                            if (($obsVarTrait != null) && ($obsVarTrait instanceof \App\Entity\TraitClass)) {
                                $observationVariable->setTrait($obsVarTrait);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the trait ontology " .$traitOntId);
                        }

                        try {
                            //code...
                            $obsVarId = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $obsVarIdTrait]);
                            if (($obsVarId != null) && ($obsVarId instanceof \App\Entity\TraitClass)) {
                                $observationVariable->setVariable($obsVarId);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the variable id ontology " .$obsVarIdTrait);
                        }

                        try {
                            //code...
                            $obsVarMethod = $entmanager->getRepository(ObservationVariableMethod::class)->findOneBy(['name' => $obsVarMethodName]);
                            if (($obsVarMethod != null) && ($obsVarMethod instanceof \App\Entity\ObservationVariableMethod)) {
                                $observationVariable->setObservationVariableMethod($obsVarMethod);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable method name " .$obsVarMethodName);
                        }

                        try {
                            //code...
                            $obsVarScale = $entmanager->getRepository(Scale::class)->findOneBy(['name' => $scaleName]);
                            if (($obsVarScale != null) && ($obsVarScale instanceof \App\Entity\Scale)) {
                                $observationVariable->setScale($obsVarScale);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the scale name " .$scaleName);
                        }

                        try {
                            //code...
                            $observationVariable->setName($obsName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable name " .$obsName);
                        }

                        try {
                            //code...
                            $observationVariable->setDescription($obsVarDesc);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation variable description " .$obsVarDesc);
                        }
                                                
                        $observationVariable->setIsActive(true);
                        $observationVariable->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($observationVariable);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalObservationVariableAfter = $repoObservationVariable->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalObservationVariableBefore == 0) {
                $this->addFlash('success', $totalObservationVariableAfter . " observation variable entities have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalObservationVariableAfter - $totalObservationVariableBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new observation variable has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation variable has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " observation variable entities have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('observation_variable_index'));
        }

        $context = [
            'title' => 'Trait Class Upload From Excel',
            'observationVariableUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('observation_variable/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/observation_variable_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'observation_variable_template_example.xlsx');
        return $response;
    }
}
