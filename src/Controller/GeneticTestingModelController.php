<?php

namespace App\Controller;

use App\Entity\GeneticTestingModel;
use App\Form\GeneticTestingModelType;
use App\Form\GeneticTestingModelUpdateType;
use App\Form\GenetingTestingModelUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GeneticTestingModelRepository;
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
 * @Route("genetic/testing/model", name="genetic_testing_model_")
 */
class GeneticTestingModelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GeneticTestingModelRepository $geneticTestingModelRepo): Response
    {
        $geneticTestingModels =  $geneticTestingModelRepo->findAll();
        $parentsOnly = $geneticTestingModelRepo->getParentsOnly();
        $context = [
            'title' => 'Genetic Testing Model List',
            'geneticTestingModels' => $geneticTestingModels,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('genetic_testing_model/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $geneticTestingModel = new GeneticTestingModel();
        $form = $this->createForm(GeneticTestingModelType::class, $geneticTestingModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $geneticTestingModel->setCreatedBy($this->getUser());
            }
            $geneticTestingModel->setIsActive(true);
            $geneticTestingModel->setCreatedAt(new \DateTime());
            $entmanager->persist($geneticTestingModel);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('genetic_testing_model_index'));
        }

        $context = [
            'title' => 'Genetic Testing Model Create',
            'geneticTestingModelForm' => $form->createView()
        ];
        return $this->render('genetic_testing_model/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GeneticTestingModel $geneticTestingModelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Genetic Testing Model Details',
            'geneticTestingModel' => $geneticTestingModelSelected
        ];
        return $this->render('genetic_testing_model/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GeneticTestingModel $geneticTestingModel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('genetic_testing_model_edit', $geneticTestingModel);
        $form = $this->createForm(GeneticTestingModelUpdateType::class, $geneticTestingModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($geneticTestingModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('genetic_testing_model_index'));
        }

        $context = [
            'title' => 'Genetic Testing Model Update',
            'geneticTestingModelForm' => $form->createView()
        ];
        return $this->render('genetic_testing_model/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GeneticTestingModel $geneticTestingModel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($geneticTestingModel->getId()) {
            $geneticTestingModel->setIsActive(!$geneticTestingModel->getIsActive());
        }
        $entmanager->persist($geneticTestingModel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $geneticTestingModel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('GeneticTestingModel_home'));
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
            $repoGeneticTestingModel = $entmanager->getRepository(GeneticTestingModel::class);
            // Query how many rows are there in the GeneticTestingModel table
            $totalGeneticTestingModelBefore = $repoGeneticTestingModel->createQueryBuilder('tab')
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
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingGeneticTestingModel = $entmanager->getRepository(GeneticTestingModel::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingGeneticTestingModel) {
                        $geneticTestingModel = new GeneticTestingModel();
                        if ($this->getUser()) {
                            $geneticTestingModel->setCreatedBy($this->getUser());
                        }
                        $geneticTestingModel->setOntologyId($ontology_id);
                        $geneticTestingModel->setName($name);
                        if ($description != null) {
                            $geneticTestingModel->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $geneticTestingModel->setParOnt($parentTermString);
                        }
                        $geneticTestingModel->setIsActive(true);
                        $geneticTestingModel->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($geneticTestingModel);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(GeneticTestingModel::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\GeneticTestingModel)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(GeneticTestingModel::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE genetic_testing_model SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalGeneticTestingModelAfter = $repoGeneticTestingModel->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGeneticTestingModelBefore == 0) {
                $this->addFlash('success', $totalGeneticTestingModelAfter . " genetic testing models have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGeneticTestingModelAfter - $totalGeneticTestingModelBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new genetic testing model has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " genetic testing model has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " genetic testing models have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('genetic_testing_model_index'));
        }

        $context = [
            'title' => 'Genetic Testing Model Upload From Excel',
            'geneticTestingModelUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('genetic_testing_model/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function GeneticTestingModelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/genetic_testing_model_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'genetic_testing_model_template_example.xls');
        return $response;
       
    }
}
