<?php

namespace App\Controller;

use App\Entity\MetaboliteClass;
use App\Form\MetaboliteClassType;
use App\Form\MetaboliteClassUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MetaboliteClassRepository;
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
 * @Route("/metabolite/class", name="metabolite_class_")
 */
class MetaboliteClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetaboliteClassRepository $metaboliteClassRepo): Response
    {
        $metaboliteClasses =  $metaboliteClassRepo->findAll();
        $context = [
            'title' => 'Metabolite Class List',
            'metaboliteClasses' => $metaboliteClasses
        ];
        return $this->render('metabolite_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metaboliteClass = new MetaboliteClass();
        $form = $this->createForm(MetaboliteClassType::class, $metaboliteClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metaboliteClass->setCreatedBy($this->getUser());
            }
            $metaboliteClass->setIsActive(true);
            $metaboliteClass->setCreatedAt(new \DateTime());
            $entmanager->persist($metaboliteClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_class_index'));
        }

        $context = [
            'title' => 'Metabolite Class Creation',
            'metaboliteClassForm' => $form->createView()
        ];
        return $this->render('metabolite_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MetaboliteClass $metaboliteClasseSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolite Class Details',
            'metaboliteClass' => $metaboliteClasseSelected
        ];
        return $this->render('metabolite_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MetaboliteClass $metaboliteClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolite_class_edit', $metaboliteClass);
        $form = $this->createForm(MetaboliteClassUpdateType::class, $metaboliteClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metaboliteClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_class_index'));
        }

        $context = [
            'title' => 'Metabolite Class Update',
            'metaboliteClassForm' => $form->createView()
        ];
        return $this->render('metabolite_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MetaboliteClass $metaboliteClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metaboliteClass->getId()) {
            $metaboliteClass->setIsActive(!$metaboliteClass->getIsActive());
        }
        $entmanager->persist($metaboliteClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metaboliteClass->getIsActive()
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
            $repoMetaboliteClass = $entmanager->getRepository(Metabolite::class);
            // Query how many rows are there in the metabolite class table
            $totalMetaboliteClassBefore = $repoMetaboliteClass->createQueryBuilder('tab')
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
                $ontology_id = $row['A'];
                $name = $row['B'];
                $parentTerm = $row['C'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null & $name != null) {
                    // check if the data is upload in the database
                    $existingMetaboliteClass = $entmanager->getRepository(MetaboliteClass::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingMetaboliteClass) {
                        $metaboliteClass = new MetaboliteClass();
                        if ($this->getUser()) {
                            $metaboliteClass->setCreatedBy($this->getUser());
                        }
                        $metaboliteClass->setName($name);
                        $metaboliteClass->setOntologyId($ontology_id);
                        $metaboliteClass->setParentTerm($parentTerm);
                        $metaboliteClass->setIsActive(true);
                        $metaboliteClass->setCreatedAt(new \DateTime());
                        $entmanager->persist($metaboliteClass);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalMetaboliteClassAfter = $repoMetaboliteClass->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMetaboliteClassBefore == 0) {
                $this->addFlash('success', $totalMetaboliteClassAfter . " metabolite classes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMetaboliteClassAfter - $totalMetaboliteClassBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new metabolite class has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolite class has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolite classes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Class Upload From Excel',
            'metaboliteClassUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('metabolite/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/metabolite_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'metabolite_template_example.xls');
        return $response;
       
    }
}