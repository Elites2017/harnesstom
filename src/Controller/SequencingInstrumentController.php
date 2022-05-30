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
        $context = [
            'title' => 'Sequencing Instrument List',
            'sequencingInstruments' => $sequencingInstruments
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
                $label = $row['A'];
                // check if the file doesn't have empty columns
                if ($label != null) {
                    // check if the data is upload in the database
                    $existingSequencingInstrument = $entmanager->getRepository(SequencingInstrument::class)->findOneBy(['label' => $label]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingSequencingInstrument) {
                        $sequencingInstrument = new SequencingInstrument();
                        if ($this->getUser()) {
                            $sequencingInstrument->setCreatedBy($this->getUser());
                        }
                        $sequencingInstrument->setLabel($label);
                        $sequencingInstrument->setIsActive(true);
                        $sequencingInstrument->setCreatedAt(new \DateTime());
                        $entmanager->persist($sequencingInstrument);
                    }
                }
            }
            $entmanager->flush();
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
            'title' => 'mls status Upload From Excel',
            'sequencingInstrumentUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('sequencing_instrument/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function factorTypeTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/sequencing_instrument_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'sequencing_instrument_template_example.xls');
        return $response;
       
    }
}
