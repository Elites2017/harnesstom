<?php

namespace App\Controller;

use App\Entity\GWASStatTest;
use App\Form\GWASStatTestType;
use App\Form\GWASStatTestUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GWASStatTestRepository;
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
 * @Route("gwas/stat/test", name="gwas_stat_test_")
 */
class GWASStatTestController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GWASStatTestRepository $gwasStatTestRepo): Response
    {
        $gwasStatTests =  $gwasStatTestRepo->findAll();
        $context = [
            'title' => 'GWAS Stat Test List',
            'gwasStatTests' => $gwasStatTests
        ];
        return $this->render('gwas_stat_test/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $gwasStatTest = new GWASStatTest();
        $form = $this->createForm(GWASStatTestType::class, $gwasStatTest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $gwasStatTest->setCreatedBy($this->getUser());
            }
            $gwasStatTest->setIsActive(true);
            $gwasStatTest->setCreatedAt(new \DateTime());
            $entmanager->persist($gwasStatTest);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_stat_test_index'));
        }

        $context = [
            'title' => 'GWAS Stat Test Creation',
            'gwasStatTestForm' => $form->createView()
        ];
        return $this->render('gwas_stat_test/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(GWASStatTest $gwasStatTestSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'GWAS Stat Test Details',
            'gwasStatTest' => $gwasStatTestSelected
        ];
        return $this->render('gwas_stat_test/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(GWASStatTest $gwasStatTest, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('gwas_stat_test_edit', $gwasStatTest);
        $form = $this->createForm(GWASStatTestUpdateType::class, $gwasStatTest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($gwasStatTest);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('gwas_stat_test_index'));
        }

        $context = [
            'title' => 'GWAS Stat Test Update',
            'gwasStatTestForm' => $form->createView()
        ];
        return $this->render('gwas_stat_test/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(GWASStatTest $gwasStatTest, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($gwasStatTest->getId()) {
            $gwasStatTest->setIsActive(!$gwasStatTest->getIsActive());
        }
        $entmanager->persist($gwasStatTest);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $gwasStatTest->getIsActive()
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
            $repoGWASStatTest = $entmanager->getRepository(GWASStatTest::class);
            // Query how many rows are there in the GWASStatTest table
            $totalGWASStatTestBefore = $repoGWASStatTest->createQueryBuilder('tab')
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
                    $existingGwasStatTest = $entmanager->getRepository(GWASStatTest::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingGwasStatTest) {
                        $gwasStatTest = new GWASStatTest();
                        if ($this->getUser()) {
                            $gwasStatTest->setCreatedBy($this->getUser());
                        }
                        $gwasStatTest->setOntologyId($ontology_id);
                        $gwasStatTest->setName($name);
                        if ($description != null) {
                            $gwasStatTest->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $gwasStatTest->setParOnt($parentTermString);
                        }
                        $gwasStatTest->setIsActive(true);
                        $gwasStatTest->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($gwasStatTest);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(GWASStatTest::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\GWASStatTest)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(GWASStatTest::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE gwasstat_test SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            // Query how many rows are there in the Country table
            $totalGWASStatTestAfter = $repoGWASStatTest->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGWASStatTestBefore == 0) {
                $this->addFlash('success', $totalGWASStatTestAfter . " gwas stat tests have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGWASStatTestAfter - $totalGWASStatTestBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new gwas stat test has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas stat test has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " gwas stat tests have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('gwas_stat_test_index'));
        }

        $context = [
            'title' => 'GWAS Stat Test Upload From Excel',
            'gWASStatTestUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('gwas_stat_test/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/gwas_stat_test_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'gwas_stat_test_template_example.xls');
        return $response;
       
    }
}