<?php

namespace App\Controller;

use App\Entity\GWASModel;
use App\Form\GWASModelType;
use App\Form\GWASModelUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GWASModelRepository;
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
 * @Route("gwas/model", name="gwas_model_")
 */
class GWASModelController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASModelRepository $gwasModelRepo): Response
    {
        $gwasModels =  $gwasModelRepo->findAll();
        $context = [
            'title' => 'GWAS Model List',
            'gwasModels' => $gwasModels
        ];
        return $this->render('gwas_model/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasModel = new GWASModel();
        $form = $this->createForm(GWASModelType::class, $gwasModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwasModel->setCreatedBy($this->getUser());
            }
            $gwasModel->setIsActive(true);
            $gwasModel->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_model_index'));
        }

        $context = [
            'title' => 'GWAS Model Creation',
            'gwasModelForm' => $form->createView()
        ];
        return $this->render('gwas_model/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASModel $gwasModelSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Model Details',
            'gwasModel' => $gwasModelSelected
        ];
        return $this->render('gwas_model/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASModel $gwasModel, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_model_edit', $gwasModel);
        $form = $this->createForm(GWASModelUpdateType::class, $gwasModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasModel);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_model_index'));
        }

        $context = [
            'title' => 'GWAS Model Update',
            'gwasModelForm' => $form->createView()
        ];
        return $this->render('gwas_model/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASModel $gwasModel, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasModel->getId()) {
            $gwasModel->setIsActive(!$gwasModel->getIsActive());
        }
        $entmanager->persist($gwasModel);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasModel->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('growthFacility_home'));
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
            $repoGWASModel = $entmanager->getRepository(GWASModel::class);
            // Query how many rows are there in the GWASModel table
            $totalGWASModelBefore = $repoGWASModel->createQueryBuilder('tab')
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
                    $existingGwasModel = $entmanager->getRepository(GWASModel::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingGwasModel) {
                        $gwasModel = new GWASModel();
                        if ($this->getUser()) {
                            $gwasModel->setCreatedBy($this->getUser());
                        }
                        $gwasModel->setOntologyId($ontology_id);
                        $gwasModel->setName($name);
                        if ($description != null) {
                            $gwasModel->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $gwasModel->setParOnt($parentTermString);
                        }
                        $gwasModel->setIsActive(true);
                        $gwasModel->setCreatedAt(new \DateTime());
                        $entmanager->persist($gwasModel);
                    }
                }
            }
            $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(GWASModel::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\GWASModel)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(GWASModel::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE gwasmodel SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalGWASModelAfter = $repoGWASModel->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGWASModelBefore == 0) {
                $this->addFlash('success', $totalGWASModelAfter . " gwas models have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGWASModelAfter - $totalGWASModelBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new gwas model has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas model has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas models have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('gwas_model_index'));
        }

        $context = [
            'title' => 'GWAS Model Upload From Excel',
            'gwasModelUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('gwas_model/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/gwas_model_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'gwas_model_template_example.xls');
        return $response;
       
    }
}