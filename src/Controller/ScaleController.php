<?php

namespace App\Controller;

use App\Entity\Scale;
use App\Form\ScaleType;
use App\Form\ScaleUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ScaleRepository;
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
 * @Route("/scale", name="scale_")
 */
class ScaleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ScaleRepository $scaleRepo): Response
    {
        $scales =  $scaleRepo->findAll();
        $context = [
            'title' => 'Scale List',
            'scales' => $scales
        ];
        return $this->render('scale/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $scale = new Scale();
        $form = $this->createForm(ScaleType::class, $scale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $scale->setCreatedBy($this->getUser());
            }
            $scale->setIsActive(true);
            $scale->setCreatedAt(new \DateTime());
            $entmanager->persist($scale);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_index'));
        }

        $context = [
            'title' => 'Scale Creation',
            'scaleForm' => $form->createView()
        ];
        return $this->render('scale/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Scale $scaleSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Scale Details',
            'scale' => $scaleSelected
        ];
        return $this->render('scale/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Scale $scale, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('scale_edit', $scale);
        $form = $this->createForm(ScaleUpdateType::class, $scale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($scale);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('scale_index'));
        }

        $context = [
            'title' => 'Scale Update',
            'scaleForm' => $form->createView()
        ];
        return $this->render('scale/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Scale $scale, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($scale->getId()) {
            $scale->setIsActive(!$scale->getIsActive());
        }
        $entmanager->persist($scale);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $scale->getIsActive()
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
            $repoScale = $entmanager->getRepository(Scale::class);
            // Query how many rows are there in the trait table
            $totalScaleBefore = $repoScale->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            // Return a number as response
            // e.g 972

            dd("Scale loading...");

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
                $description = $row['C'];
                $chebiMass = $row['D'];
                $chebiMonoscopic = $row['E'];
                $synonym = $row['F'];
                $chebiLink = $row['G'];
                $parentTerm = $row['I'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingScale = $entmanager->getRepository(Scale::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingScale) {
                        $scale = new Scale();
                        $scaleUnit = $entmanager->getRepository(Unit::class)->findOneBy(['iso3' => $UnitISO3]);
                        if (($scaleUnit != null) && ($scaleUnit instanceof \App\Entity\Unit)) {
                            $scale->setUnit($scaleUnit);
                        }
                        $scaleDataType = $entmanager->getRepository(DataType::class)->findOneBy(['iso3' => $DataTypeISO3]);
                        if (($scaleDataType != null) && ($scaleDataType instanceof \App\Entity\DataType)) {
                            $scale->setDataType($scaleDataType);
                        }

                        if ($this->getUser()) {
                            $scale->setCreatedBy($this->getUser());
                        }
                        // $scale->setOntologyId($ontology_id);
                        // $scale->setName($name);
                        // if ($description != null) {
                        //     $scale->setDescription($description);
                        // }
                        // if ($parentTerm != null) {
                        //     $scale->setParOnt($parentTerm);
                        // }
                        // $scale->setChebiMass($chebiMass);
                        // $scale->setChebiMonoIsoTopicMass($chebiMonoscopic);
                        // split the text to array based on that patern
                        $synonym = explode("|", $synonym);
                        //var_dump($chebiMonoscopic);
                        //$arr [] = $synonym;
                        //dd($arr);
                        // $scale->setSynonym($synonym);
                        // $scale->setChebiLink($chebiLink);

                        $scale->setIsActive(true);
                        $scale->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($scale);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            $totalScaleAfter = $repoScale->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalScaleBefore == 0) {
                $this->addFlash('success', $totalScaleAfter . " scales have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalScaleAfter - $totalScaleBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new scale has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " scale has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " scales have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('scale_index'));
        }

        $context = [
            'title' => 'Scale Upload From Excel',
            'scaleUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('scale/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/scale_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'scale_template_example.xls');
        return $response;
       
    }
}
