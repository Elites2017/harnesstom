<?php

namespace App\Controller;

use App\Entity\MethodClass;
use App\Form\MethodClassType;
use App\Form\MethodClassUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MethodClassRepository;
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
 * @Route("/method/class", name="method_class_")
 */
class MethodClassController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MethodClassRepository $methodClassRepo): Response
    {
        $methodClasses =  $methodClassRepo->findAll();
        $context = [
            'title' => 'Method Class List',
            'methodClasses' => $methodClasses
        ];
        return $this->render('method_class/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $methodClass = new MethodClass();
        $form = $this->createForm(MethodClassType::class, $methodClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $methodClass->setCreatedBy($this->getUser());
            }
            $methodClass->setIsActive(true);
            $methodClass->setCreatedAt(new \DateTime());
            $entmanager->persist($methodClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('method_class_index'));
        }

        $context = [
            'title' => 'Method Class Creation',
            'methodClassForm' => $form->createView()
        ];
        return $this->render('method_class/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MethodClass $methodClassSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Method Class Details',
            'methodClass' => $methodClassSelected
        ];
        return $this->render('method_class/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MethodClass $methodClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('method_class_edit', $methodClass);
        $form = $this->createForm(MethodClassUpdateType::class, $methodClass);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($methodClass);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('method_class_index'));
        }

        $context = [
            'title' => 'Method Class Update',
            'methodClassForm' => $form->createView()
        ];
        return $this->render('method_class/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MethodClass $methodClass, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($methodClass->getId()) {
            $methodClass->setIsActive(!$methodClass->getIsActive());
        }
        $entmanager->persist($methodClass);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $methodClass->getIsActive()
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
            $repoMethodClass = $entmanager->getRepository(MethodClass::class);
            // Query how many rows are there in the MethodClass table
            $totalMethodClassBefore = $repoMethodClass->createQueryBuilder('tab')
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
                $name = $row['A'];
                $description = $row['B'];
                // check if the file doesn't have empty columns
                if ($name != null && $description != null) {
                    // check if the data is upload in the database
                    $existingMethodClass = $entmanager->getRepository(MethodClass::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingMethodClass) {
                        $MethodClass = new MethodClass();
                        if ($this->getUser()) {
                            $MethodClass->setCreatedBy($this->getUser());
                        }
                        $MethodClass->setName($name);
                        $MethodClass->setDescription($description);
                        $MethodClass->setIsActive(true);
                        $MethodClass->setCreatedAt(new \DateTime());
                        $entmanager->persist($MethodClass);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalMethodClassAfter = $repoMethodClass->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMethodClassBefore == 0) {
                $this->addFlash('success', $totalMethodClassAfter . " method classes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMethodClassAfter - $totalMethodClassBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new method class has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " method class has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " method classes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('method_class_index'));
        }

        $context = [
            'title' => 'Method Class Upload From Excel',
            'methodClassUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('method_class/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function factorTypeTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/method_class_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'method_class_template_example.xls');
        return $response;
       
    }
}
