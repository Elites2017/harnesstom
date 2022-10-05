<?php

namespace App\Controller;

use App\Entity\Enzyme;
use App\Form\EnzymeType;
use App\Form\EnzymeUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\EnzymeRepository;
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
 * @Route("/enzyme", name="enzyme_")
 */
class EnzymeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EnzymeRepository $enzymeRepo): Response
    {
        $enzymes =  $enzymeRepo->findAll();
        $context = [
            'title' => 'Enzyme',
            'enzymes' => $enzymes
        ];
        return $this->render('enzyme/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $enzyme = new Enzyme();
        $form = $this->createForm(EnzymeType::class, $enzyme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $enzyme->setCreatedBy($this->getUser());
            }
            $enzyme->setIsActive(true);
            $enzyme->setCreatedAt(new \DateTime());
            $entmanager->persist($enzyme);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('enzyme_index'));
        }

        $context = [
            'title' => 'Enzyme Creation',
            'enzymeForm' => $form->createView()
        ];
        return $this->render('enzyme/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Enzyme $enzymeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Enzyme Details',
            'enzyme' => $enzymeSelected
        ];
        return $this->render('enzyme/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Enzyme $enzyme, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('enzyme_edit', $enzyme);
        $form = $this->createForm(EnzymeUpdateType::class, $enzyme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($enzyme);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('enzyme_index'));
        }

        $context = [
            'title' => 'Enzyme Update',
            'enzymeForm' => $form->createView()
        ];
        return $this->render('enzyme/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Enzyme $enzyme, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($enzyme->getId()) {
            $enzyme->setIsActive(!$enzyme->getIsActive());
        }
        $entmanager->persist($enzyme);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $enzyme->getIsActive()
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
            $repoEnzyme = $entmanager->getRepository(Enzyme::class);
            // Query how many rows are there in the Enzyme table
            $totalEnzymeBefore = $repoEnzyme->createQueryBuilder('tab')
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
                    $existingEnzyme = $entmanager->getRepository(Enzyme::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingEnzyme) {
                        $enzyme = new Enzyme();
                        if ($this->getUser()) {
                            $enzyme->setCreatedBy($this->getUser());
                        }
                        $enzyme->setOntologyId($ontology_id);
                        $enzyme->setName($name);
                        if ($description != null) {
                            $enzyme->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $enzyme->setParOnt($parentTermString);
                        }
                        $enzyme->setIsActive(true);
                        $enzyme->setCreatedAt(new \DateTime());
                        $entmanager->persist($enzyme);
                    }
                }
            }
            $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(Enzyme::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\Enzyme)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(Enzyme::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE enzyme SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }
            
            // Query how many rows are there in the Country table
            $totalEnzymeAfter = $repoEnzyme->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalEnzymeBefore == 0) {
                $this->addFlash('success', $totalEnzymeAfter . " Enzymes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalEnzymeAfter - $totalEnzymeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Enzyme has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Enzyme has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " Enzymes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('enzyme_index'));
        }

        $context = [
            'title' => 'Enzyme Upload From Excel',
            'enzymeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('enzyme/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/enzyme_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'enzyme_template_example.xls');
        return $response;
       
    }
}
