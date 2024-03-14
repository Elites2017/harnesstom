<?php

namespace App\Controller;

use App\Entity\QTLStatistic;
use App\Form\QTLStatisticType;
use App\Form\QTLStatisticUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\QTLStatisticRepository;
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
 * @Route("/qtl/statistic", name="qtl_statistic_")
 */
class QTLStatisticController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLStatisticRepository $qtlStatisticRepo): Response
    {
        $qtlStatistics =  $qtlStatisticRepo->findAll();
        $parentsOnly = $qtlStatisticRepo->getParentsOnly();
        $context = [
            'title' => 'QTL Statistic',
            'qtlStatistics' => $qtlStatistics,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('qtl_statistic/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlStatistic = new QTLStatistic();
        $form = $this->createForm(QTLStatisticType::class, $qtlStatistic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlStatistic->setCreatedBy($this->getUser());
            }
            $qtlStatistic->setIsActive(true);
            $qtlStatistic->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlStatistic);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('qtl_statistic_index'));
        }

        $context = [
            'title' => 'QTL Statistic Creation',
            'qtlStatisticForm' => $form->createView()
        ];
        return $this->render('qtl_statistic/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(QTLStatistic $qtlStatisticSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'QTL Statistic Details',
            'qtlStatistic' => $qtlStatisticSelected
        ];
        return $this->render('qtl_statistic/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(QTLStatistic $qtlStatistic, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('qtl_statistic_edit', $qtlStatistic);
        $form = $this->createForm(QTLStatisticUpdateType::class, $qtlStatistic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($qtlStatistic);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('qtl_statistic_index'));
        }

        $context = [
            'title' => 'QTL Statistic Update',
            'qtlStatisticForm' => $form->createView()
        ];
        return $this->render('qtl_statistic/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(QTLStatistic $qtlStatistic, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($qtlStatistic->getId()) {
            $qtlStatistic->setIsActive(!$qtlStatistic->getIsActive());
        }
        $entmanager->persist($qtlStatistic);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $qtlStatistic->getIsActive()
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
            $repoQTLStatistic = $entmanager->getRepository(QTLStatistic::class);
            // Query how many rows are there in the QTLStatistic table
            $totalQTLStatisticBefore = $repoQTLStatistic->createQueryBuilder('tab')
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
                    $existingQTLStatistic = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingQTLStatistic) {
                        $qTLStatistic = new QTLStatistic();
                        if ($this->getUser()) {
                            $qTLStatistic->setCreatedBy($this->getUser());
                        }
                        $qTLStatistic->setOntologyId($ontology_id);
                        $qTLStatistic->setName($name);
                        if ($description != null) {
                            $qTLStatistic->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $qTLStatistic->setParOnt($parentTermString);
                        }
                        $qTLStatistic->setIsActive(true);
                        $qTLStatistic->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($qTLStatistic);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\QTLStatistic)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(QTLStatistic::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE qtlstatistic SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Country table
            $totalQTLStatisticAfter = $repoQTLStatistic->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalQTLStatisticBefore == 0) {
                $this->addFlash('success', $totalQTLStatisticAfter . " qtl statistics have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalQTLStatisticAfter - $totalQTLStatisticBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new qtl statistic has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl statistic has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " qtl statistics have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('qtl_statistic_index'));
        }

        $context = [
            'title' => 'QLT Statistic Upload From Excel',
            'qTLStatisticUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('qtl_statistic/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/qtl_statistic_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qtl_statistic_template_example.xls');
        return $response;
       
    }
}

