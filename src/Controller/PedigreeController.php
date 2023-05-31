<?php

namespace App\Controller;

use App\Entity\Cross;
use App\Entity\Generation;
use App\Entity\Pedigree;
use App\Entity\Progeny;
use App\Form\PedigreeType;
use App\Form\PedigreeUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\PedigreeRepository;
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
 * @Route("/pedigree", name="pedigree_")
 */
class PedigreeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PedigreeRepository $pedigreeRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $pedigrees =  $pedigreeRepo->findAll();
        $context = [
            'title' => 'Pedigree',
            'pedigrees' => $pedigrees
        ];
        return $this->render('pedigree/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $pedigree = new Pedigree();
        $form = $this->createForm(PedigreeType::class, $pedigree);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $pedigree->setCreatedBy($this->getUser());
            }
            //dd($pedigree);
            if ($pedigree->getGeneration() !== "P") {
                $progeny =  new Progeny();
                // We had a many to many relationship which we have realized that was a many to one relationship
                $progeny->setPedigreeGermplasm($pedigree->getGermplasm()[0]);
                $progeny->setProgenyId($pedigree->getPedigreeEntryId());
                $progeny->setProgenyCross($pedigree->getPedigreeCross());
                $progeny->setProgenyParent1($pedigree->getPedigreeCross()->getParent1());
                $progeny->setProgenyParent2($pedigree->getPedigreeCross()->getParent2());
                $entmanager->persist($progeny);
                //dd($progeny->getProgenyParent1());
                //$progeny->setProgenyId($pedigree->getPedigreeEntryID());
                //$progeny->addProgenyCross($pedigree->getPedigreeCross());
                //dd("Progeny will be created ", $progeny);
            }
            //dd("Progeny should not be created");
            $pedigree->setIsActive(true);
            $pedigree->setCreatedAt(new \DateTime());
            $entmanager->persist($pedigree);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('pedigree_index'));
        }

        $context = [
            'title' => 'Pedigree Creation',
            'pedigreeForm' => $form->createView()
        ];
        return $this->render('pedigree/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Pedigree $pedigreeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Pedigree Details',
            'pedigree' => $pedigreeSelected
        ];
        return $this->render('pedigree/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Pedigree $pedigree, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('pedigree_edit', $pedigree);
        $form = $this->createForm(PedigreeUpdateType::class, $pedigree);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $pedigree->setCreatedBy($this->getUser());
            }
            
            if ($pedigree->getGeneration() !== "P") {
                $progeny =  new Progeny();
                // We had a many to many relationship which we have realized that was a many to one relationship
                $progeny->setPedigreeGermplasm($pedigree->getGermplasm()[0]);
                $progeny->setProgenyId($pedigree->getPedigreeEntryId());
                $progeny->setProgenyCross($pedigree->getPedigreeCross());
                $progeny->setProgenyParent1($pedigree->getPedigreeCross()->getParent1());
                $progeny->setProgenyParent2($pedigree->getPedigreeCross()->getParent2());
                $entmanager->persist($progeny);
                //dd($progeny->getProgenyParent1());
                //$progeny->setProgenyId($pedigree->getPedigreeEntryID());
                //$progeny->addProgenyCross($pedigree->getPedigreeCross());
                //dd("Progeny will be created ", $progeny);
            }
            $entmanager->persist($pedigree);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('pedigree_index'));
        }

        $context = [
            'title' => 'Pedigree Update',
            'pedigreeForm' => $form->createView()
        ];
        return $this->render('pedigree/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Pedigree $pedigree, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($pedigree->getId()) {
            $pedigree->setIsActive(!$pedigree->getIsActive());
        }
        $entmanager->persist($pedigree);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $pedigree->getIsActive()
        ], 200);
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
            $repoPedigree = $entmanager->getRepository(Pedigree::class);
            // Query how many rows are there in the Pedigree table
            $totalPedigreeBefore = $repoPedigree->createQueryBuilder('tab')
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
                $pedigreeEntryId = $row['A'];
                $germplasmDbId = $row['B'];
                $crossName = $row['C'];
                $generationOntId = $row['D'];
                $ancestorPedigreeId = $row['E'];
                // check if the file doesn't have empty columns
                if ($pedigreeEntryId != null && $germplasmDbId != null && $crossName != null && $generationOntId != null) {
                    // check if the data is upload in the database
                    $existingPedigree = $entmanager->getRepository(Pedigree::class)->findOneBy(['pedigreeEntryID' => $pedigreeEntryId]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingPedigree) {
                        $pedigree = new Pedigree();
                        if ($this->getUser()) {
                            $pedigree->setCreatedBy($this->getUser());
                        }

                        try {
                            //code...
                            $pedigree->setPedigreeEntryID($pedigreeEntryId);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the pedigree entry ID " .$pedigreeEntryId);
                        }

                        if ($ancestorPedigreeId) {
                            try {
                                //code...
                                $pedigree->setAncestorPedigreeEntryID($ancestorPedigreeId);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the ancesstor pedigree ID " .$ancestorPedigreeId);
                            }   
                        }

                        try {
                            //code...
                            $pedigreeCross = $entmanager->getRepository(Cross::class)->findOneBy(['name' => $crossName]);
                            if (($pedigreeCross != null) && ($pedigreeCross instanceof \App\Entity\Cross)) {
                                $pedigree->setPedigreeCross($pedigreeCross);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the cross name " .$crossName);
                        }

                        try {
                            //code...
                            $pedigreeGeneration = $entmanager->getRepository(Generation::class)->findOneBy(['ontology_id' => $generationOntId]);
                            if (($pedigreeGeneration != null) && ($pedigreeGeneration instanceof \App\Entity\Generation)) {
                                $pedigree->setGeneration($pedigreeGeneration);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the generation ontology ID " .$generationOntId);
                        }

                        try {
                            //code...
                            $pedigreeGermplasm = $entmanager->getRepository(Germplasm::class)->findOneBy(['germplasmID' => $germplasmDbId]);
                            if (($pedigreeGermplasm != null) && ($pedigreeGermplasm instanceof \App\Entity\Germplasm)) {
                                $pedigree->addGermplasm($pedigreeGermplasm);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the germplasm " .$germplasmDbId);
                        }

                        $pedigree->setIsActive(true);
                        $pedigree->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($pedigree);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                } else {
                    $this->addFlash('danger', " The pedigree entry ID, the cross name, the germplasm ID and the generation ontology ID can not be empty, provide them and try again");
                }
            }
            
            // Query how many rows are there in the table
            $totalPedigreeAfter = $repoPedigree->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalPedigreeBefore == 0) {
                $this->addFlash('success', $totalPedigreeAfter . " pedigrees have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalPedigreeAfter - $totalPedigreeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new pedigree has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " pedigree has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " pedigrees have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('pedigree_index'));
        }

        $context = [
            'title' => 'Pedigree Upload From Excel',
            'pedigreeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('pedigree/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/pedigree_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'pedigree_template_example.xlsx');
        return $response;
       
    }
}
