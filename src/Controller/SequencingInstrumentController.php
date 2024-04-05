<?php

namespace App\Controller;

use App\Entity\SequencingInstrument;
use App\Form\SequencingInstrumentType;
use App\Form\SequencingInstrumentUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SequencingInstrumentRepository;
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
 * @Route("/sequencing/instrument", name="sequencing_instrument_")
 */
class SequencingInstrumentController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SequencingInstrumentRepository $sequencingInstrumentRepo): Response
    {
        $sequencingInstruments =  $sequencingInstrumentRepo->findAll();
        $parentsOnly = $sequencingInstrumentRepo->getParentsOnly();
        $context = [
            'title' => 'Sequencing Instrument List',
            'sequencingInstruments' => $sequencingInstruments,
            'parentsOnly' => $parentsOnly
        ];
        return $this->render('sequencing_instrument/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sequencingInstrument = new SequencingInstrument();
        $form = $this->createForm(SequencingInstrumentType::class, $sequencingInstrument);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $sequencingInstrument->setCreatedBy($this->getUser());
            }
            $sequencingInstrument->setIsActive(true);
            $sequencingInstrument->setCreatedAt(new \DateTime());
            $entmanager->persist($sequencingInstrument);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('sequencing_instrument_index'));
        }

        $context = [
            'title' => 'Sequencing Instrument Creation',
            'sequencingInstrumentForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(SequencingInstrument $sequencingInstrumentSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Sequencing Instrument Details',
            'sequencingInstrument' => $sequencingInstrumentSelected
        ];
        return $this->render('sequencing_instrument/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(SequencingInstrument $sequencingInstrument, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('sequencing_instrument_edit', $sequencingInstrument);
        $form = $this->createForm(SequencingInstrumentUpdateType::class, $sequencingInstrument);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($sequencingInstrument);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('sequencing_instrument_index'));
        }

        $context = [
            'title' => 'Sequencing Instrument Update',
            'sequencingInstrumentForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(SequencingInstrument $sequencingInstrument, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($sequencingInstrument->getId()) {
            $sequencingInstrument->setIsActive(!$sequencingInstrument->getIsActive());
        }
        $entmanager->persist($sequencingInstrument);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $sequencingInstrument->getIsActive()
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
            $repoSequencingInstrument = $entmanager->getRepository(SequencingInstrument::class);
            // Query how many rows are there in the SequencingInstrument table
            $totalSequencingInstrumentBefore = $repoSequencingInstrument->createQueryBuilder('tab')
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
                    $existingSequencingInstrument = $entmanager->getRepository(SequencingInstrument::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingSequencingInstrument) {
                        $sequencingInstrument = new SequencingInstrument();
                        if ($this->getUser()) {
                            $sequencingInstrument->setCreatedBy($this->getUser());
                        }
                        $sequencingInstrument->setOntologyId($ontology_id);
                        $sequencingInstrument->setName($name);
                        if ($description != null) {
                            $sequencingInstrument->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $sequencingInstrument->setParOnt($parentTermString);
                        }
                        $sequencingInstrument->setIsActive(true);
                        $sequencingInstrument->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($sequencingInstrument);
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
                    $ontologyIdParentTerm = $entmanager->getRepository(SequencingInstrument::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\SequencingInstrument)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(SequencingInstrument::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE sequencing_instrument SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                } 
            }

            // Query how many rows are there in the Country table
            $totalSequencingInstrumentAfter = $repoSequencingInstrument->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSequencingInstrumentBefore == 0) {
                $this->addFlash('success', $totalSequencingInstrumentAfter . " sequencing instruments have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSequencingInstrumentAfter - $totalSequencingInstrumentBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new sequencing instrument has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " sequencing instrument has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " sequencing instruments have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('sequencing_instrument_index'));
        }

        $context = [
            'title' => 'Sequencing Instrument Upload From Excel',
            'sequencingInstrumentUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/sequencing_instrument_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'sequencing_instrument_template_example.xls');
        return $response;
       
    }
}
