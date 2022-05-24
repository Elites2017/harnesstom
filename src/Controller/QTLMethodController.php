<?php

namespace App\Controller;

use App\Entity\QTLMethod;
use App\Form\QTLMethodType;
use App\Form\QTLMethodUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\QTLMethodRepository;
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
 * @Route("/qtl/method", name="qtl_method_")
 */
class QTLMethodController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLMethodRepository $qtlMethodRepo): Response
    {
        $qtlMethods =  $qtlMethodRepo->findAll();
        $context = [
            'title' => 'QTL Method List',
            'qtlMethods' => $qtlMethods
        ];
        return $this->render('qtl_method/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlMethod = new QTLMethod();
        $form = $this->createForm(QTLMethodType::class, $qtlMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlMethod->setCreatedBy($this->getUser());
            }
            $qtlMethod->setIsActive(true);
            $qtlMethod->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_method_index'));
        }

        $context = [
            'title' => 'QTL Method Creation',
            'qtlMethodForm' => $form->createView()
        ];
        return $this->render('qtl_method/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLMethod $qtlMethodSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Method Details',
            'qtlMethod' => $qtlMethodSelected
        ];
        return $this->render('qtl_method/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLMethod $qtlMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_method_edit', $qtlMethod);
        $form = $this->createForm(QTLMethodUpdateType::class, $qtlMethod);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlMethod);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_method_index'));
        }

        $context = [
            'title' => 'QTL Method Update',
            'qtlMethodForm' => $form->createView()
        ];
        return $this->render('qtl_method/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLMethod $qtlMethod, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlMethod->getId()) {
            $qtlMethod->setIsActive(!$qtlMethod->getIsActive());
        }
        $entmanager->persist($qtlMethod);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlMethod->getIsActive()
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
            $repoQTLMethod = $entmanager->getRepository(QTLMethod::class);
            // Query how many rows are there in the QTLMethod table
            $totalQTLMethodBefore = $repoQTLMethod->createQueryBuilder('tab')
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
                if ($ontology_id != null & $name != null) {
                    // check if the data is upload in the database
                    $existingQTLMethod = $entmanager->getRepository(QTLMethod::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingQTLMethod) {
                        $qTLMethod = new QTLMethod();
                        if ($this->getUser()) {
                            $qTLMethod->setCreatedBy($this->getUser());
                        }
                        $qTLMethod->setName($name);
                        $qTLMethod->setOntologyId($ontology_id);
                        $qTLMethod->setParentTerm($parentTerm);
                        $qTLMethod->setIsActive(true);
                        $qTLMethod->setCreatedAt(new \DateTime());
                        $entmanager->persist($qTLMethod);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalQTLMethodAfter = $repoQTLMethod->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalQTLMethodBefore == 0) {
                $this->addFlash('success', $totalQTLMethodAfter . " qtl methods have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalQTLMethodAfter - $totalQTLMethodBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new qtl method has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl method has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl methods have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('qtl_method_index'));
        }

        $context = [
            'title' => 'Identification Level Upload From Excel',
            'qTLMethodUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('qtl_method/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function factorTypeTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/qtl_method_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qtl_method_template_example.xls');
        return $response;
       
    }
}

