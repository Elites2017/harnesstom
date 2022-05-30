<?php

namespace App\Controller;

use App\Entity\CiCriteria;
use App\Form\CiCriteriaType;
use App\Form\CiCriteriaUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\CiCriteriaRepository;
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
 * @Route("ci/criteria", name="ci_criteria_")
 */
class CiCriteriaController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CiCriteriaRepository $ciCriteriaRepo): Response
    {
        $ciCriterias =  $ciCriteriaRepo->findAll();
        $context = [
            'title' => 'Ci Criteria List',
            'ciCriterias' => $ciCriterias
        ];
        return $this->render('ci_criteria/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $ciCriteria = new CiCriteria();
        $form = $this->createForm(CiCriteriaType::class, $ciCriteria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $ciCriteria->setCreatedBy($this->getUser());
            }
            $ciCriteria->setIsActive(true);
            $ciCriteria->setCreatedAt(new \DateTime());
            $entmanager->persist($ciCriteria);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('ci_criteria_index'));
        }

        $context = [
            'title' => 'Ci Criteria Creation',
            'ciCriteriaForm' => $form->createView()
        ];
        return $this->render('ci_criteria/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(CiCriteria $ciCriteriaSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Ci Criteria Details',
            'ciCriteria' => $ciCriteriaSelected
        ];
        return $this->render('ci_criteria/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(CiCriteria $ciCriteria, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('ci_criteria_edit', $ciCriteria);
        $form = $this->createForm(CiCriteriaUpdateType::class, $ciCriteria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($ciCriteria);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('ci_criteria_index'));
        }

        $context = [
            'title' => 'Ci Criteria Update',
            'ciCriteriaForm' => $form->createView()
        ];
        return $this->render('ci_criteria/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(CiCriteria $ciCriteria, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($ciCriteria->getId()) {
            $ciCriteria->setIsActive(!$ciCriteria->getIsActive());
        }
        $entmanager->persist($ciCriteria);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $ciCriteria->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('ciCriteria_home'));
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
            $repoCiCriteria = $entmanager->getRepository(CiCriteria::class);
            // Query how many rows are there in the CiCriteria table
            $totalCiCriteriaBefore = $repoCiCriteria->createQueryBuilder('tab')
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
                $parentTerm = $row['C'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingCiCriteria = $entmanager->getRepository(CiCriteria::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingCiCriteria) {
                        $ciCriteria = new CiCriteria();
                        if ($this->getUser()) {
                            $ciCriteria->setCreatedBy($this->getUser());
                        }
                        $ciCriteria->setOntologyId($ontology_id);
                        $ciCriteria->setName($name);
                        $ciCriteria->setParentTerm($parentTerm);
                        $ciCriteria->setIsActive(true);
                        $ciCriteria->setCreatedAt(new \DateTime());
                        $entmanager->persist($ciCriteria);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalCiCriteriaAfter = $repoCiCriteria->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCiCriteriaBefore == 0) {
                $this->addFlash('success', $totalCiCriteriaAfter . " ci criterias have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCiCriteriaAfter - $totalCiCriteriaBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new ci criteria has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " ci criteria has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " ci criterias have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('ci_criteria_index'));
        }

        $context = [
            'title' => 'Ci Criteria Upload From Excel',
            'ciCriteriaUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('ci_criteria/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/ci_criteria_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'ci_criteria_template_example.xls');
        return $response;
       
    }
}