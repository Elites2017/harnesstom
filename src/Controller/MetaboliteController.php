<?php

namespace App\Controller;

use App\Entity\Metabolite;
use App\Form\MetaboliteType;
use App\Form\MetaboliteUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MetaboliteRepository;
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
 * @Route("/metabolite", name="metabolite_")
 */
class MetaboliteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetaboliteRepository $metaboliteRepo): Response
    {
        $metabolites =  $metaboliteRepo->findAll();
        $context = [
            'title' => 'Metabolite List',
            'metabolites' => $metabolites
        ];
        return $this->render('metabolite/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metabolite = new Metabolite();
        $form = $this->createForm(MetaboliteType::class, $metabolite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metabolite->setCreatedBy($this->getUser());
            }
            $metabolite->setIsActive(true);
            $metabolite->setCreatedAt(new \DateTime());
            $entmanager->persist($metabolite);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Creation',
            'metaboliteForm' => $form->createView()
        ];
        return $this->render('metabolite/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Metabolite $metaboliteSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolite Details',
            'metabolite' => $metaboliteSelected
        ];
        return $this->render('metabolite/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Metabolite $metabolite, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolite_edit', $metabolite);
        $form = $this->createForm(MetaboliteUpdateType::class, $metabolite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metabolite);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Update',
            'metaboliteForm' => $form->createView()
        ];
        return $this->render('metabolite/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Metabolite $metabolite, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metabolite->getId()) {
            $metabolite->setIsActive(!$metabolite->getIsActive());
        }
        $entmanager->persist($metabolite);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metabolite->getIsActive()
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
            $repoMetabolite = $entmanager->getRepository(Metabolite::class);
            // Query how many rows are there in the metabolite table
            $totalMetaboliteBefore = $repoMetabolite->createQueryBuilder('tab')
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
                    $existingMetabolite = $entmanager->getRepository(Metabolite::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingMetabolite) {
                        $metabolite = new Metabolite();
                        if ($this->getUser()) {
                            $metabolite->setCreatedBy($this->getUser());
                        }
                        $metabolite->setIsActive(true);
                        $metabolite->setCreatedAt(new \DateTime());

                        try {
                            //code...
                            $entmanager->persist($metabolite);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // Query how many rows are there in the Country table
            $totalMetaboliteAfter = $repoMetabolite->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalMetaboliteBefore == 0) {
                $this->addFlash('success', $totalMetaboliteAfter . " metabolites have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalMetaboliteAfter - $totalMetaboliteBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new metabolite has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolite has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolites have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('metabolite_index'));
        }

        $context = [
            'title' => 'Metabolite Upload From Excel',
            'metaboliteUploadFromExcelForm' => $form->createView()
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

