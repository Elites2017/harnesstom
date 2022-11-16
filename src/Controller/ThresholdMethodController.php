<?php

namespace App\Controller;

use App\Entity\ThresholdMethod;
use App\Form\ThresholdMethodType;
use App\Form\ThresholdMethodUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ThresholdMethodRepository;
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
 * @Route("/threshold/method", name="threshold_method_")
 */
class ThresholdMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ThresholdMethodRepository $thresholdMethodRepo): Response
    {
        $thresholdMethods =  $thresholdMethodRepo->findAll();
        $context = [
            'title' => 'Threshold Method List',
            'thresholdMethods' => $thresholdMethods
        ];
        return $this->render('threshold_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $thresholdMethod = new ThresholdMethod();
        $form = $this->createForm(ThresholdMethodType::class, $thresholdMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $thresholdMethod->setCreatedBy($this->getUser());
            }
            $thresholdMethod->setIsActive(true);
            $thresholdMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($thresholdMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('threshold_method_index'));
        }

        $context = [
            'title' => 'Threshold Method Creation',
            'thresholdMethodForm' => $form->createView()
        ];
        return $this->render('threshold_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ThresholdMethod $thresholdMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Threshold Method Details',
            'thresholdMethod' => $thresholdMethodSelected
        ];
        return $this->render('threshold_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ThresholdMethod $thresholdMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('threshold_method_edit', $thresholdMethod);
        $form = $this->createForm(ThresholdMethodUpdateType::class, $thresholdMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($thresholdMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('threshold_method_index'));
        }

        $context = [
            'title' => 'Threshold Method Update',
            'thresholdMethodForm' => $form->createView()
        ];
        return $this->render('threshold_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ThresholdMethod $thresholdMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($thresholdMethod->getId()) {
            $thresholdMethod->setIsActive(!$thresholdMethod->getIsActive());
        }
        $entmanager->persist($thresholdMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $thresholdMethod->getIsActive()
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
            $repoThresholdMethod = $entmanager->getRepository(ThresholdMethod::class);
            // Query how many rows are there in the thresholdMethod table
            $totalthresholdMethodBefore = $repoThresholdMethod->createQueryBuilder('tab')
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
                    $existingThresholdMethod = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingThresholdMethod) {
                        $thresholdMethod = new ThresholdMethod();
                        if ($this->getUser()) {
                            $thresholdMethod->setCreatedBy($this->getUser());
                        }
                        $thresholdMethod->setOntologyId($ontology_id);
                        $thresholdMethod->setName($name);
                        if ($description != null) {
                            $thresholdMethod->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $thresholdMethod->setParOnt($parentTermString);
                        }
                        $thresholdMethod->setIsActive(true);
                        $thresholdMethod->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($thresholdMethod);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\ThresholdMethod)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(ThresholdMethod::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE threshold_method SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalthresholdMethodAfter = $repoThresholdMethod->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalthresholdMethodBefore == 0) {
                $this->addFlash('success', $totalthresholdMethodAfter . " threshold methods have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalthresholdMethodAfter - $totalthresholdMethodBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new threshold method has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " threshold method has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " threshold methods have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('threshold_method_index'));
        }

        $context = [
            'title' => 'Threshold Method Upload From Excel',
            'thresholdMethodUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('threshold_method/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/threshold_method_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'threshold_method_template_example.xls');
        return $response;
       
    }
}

