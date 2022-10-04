<?php

namespace App\Controller;

use App\Entity\Generation;
use App\Form\GenerationType;
use App\Form\GenerationUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\GenerationRepository;
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
 * @Route("/generation", name="generation_")
 */
class GenerationController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GenerationRepository $generationRepo): Response
    {
        $generations =  $generationRepo->findAll();
        $context = [
            'title' => 'Generation',
            'generations' => $generations
        ];
        return $this->render('generation/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $generation = new Generation();
        $form = $this->createForm(GenerationType::class, $generation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $generation->setCreatedBy($this->getUser());
            }
            $generation->setIsActive(true);
            $generation->setCreatedAt(new \DateTime());
            $entmanager->persist($generation);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('generation_index'));
        }

        $context = [
            'title' => 'Generation Creation',
            'generationForm' => $form->createView()
        ];
        return $this->render('generation/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Generation $generationselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Generation Details',
            'generation' => $generationselected
        ];
        return $this->render('generation/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Generation $generation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('generation_edit', $generation);
        $form = $this->createForm(GenerationUpdateType::class, $generation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($generation);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('generation_index'));
        }

        $context = [
            'title' => 'Generation Update',
            'generationForm' => $form->createView()
        ];
        return $this->render('generation/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Generation $generation, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($generation->getId()) {
            $generation->setIsActive(!$generation->getIsActive());
        }
        $entmanager->persist($generation);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $generation->getIsActive()
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
            $repogeneration = $entmanager->getRepository(Generation::class);
            // Query how many rows are there in the Country table
            $totalGenerationBefore = $repogeneration->createQueryBuilder('a')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(a.id)')
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
                $ontologyId = $row['C'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($name != null) {
                    // check if the data has already been uploaded in the database
                    $existingGeneration = $entmanager->getRepository(Generation::class)->findOneBy(['ontology_id' => $ontologyId]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingGeneration) {
                        $generation = new Generation();
                        // set the parent term based on it's ontology ID generated by the database
                        // use the received parentTerm to search its ontologyID as it is a self reflecting / relationship table
                        $generationntologyIdParentTerm = $entmanager->getRepository(Generation::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if (($generationntologyIdParentTerm != null) && ($generationntologyIdParentTerm instanceof \App\Entity\Generation)) {
                            $generation->setParentTerm($generationntologyIdParentTerm);
                        }
                        if ($this->getUser()) {
                            $generation->setCreatedBy($this->getUser());
                        }
                        $generation->setName($name);
                        $generation->setOntologyId($ontologyId);
                        $generation->setDescription($description);
                        $generation->setIsActive(true);
                        $generation->setCreatedAt(new \DateTime());
                        $entmanager->persist($generation);
                    }
                } else {
                    $this->addFlash('danger', "Check your file again, Name and ontology_id must be filled / provide");
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the generation table
            $totalGenerationfter = $repogeneration->createQueryBuilder('a')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalGenerationBefore == 0) {
                $this->addFlash('success', $totalGenerationfter . " generation have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalGenerationfter - $totalGenerationBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new generation has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " generation has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " generations have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('generation_index'));
        }

        $context = [
            'title' => 'Generation Upload From Excel',
            'generationUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('generation/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function generationTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/generation_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'generation_template_example.xls');
        return $response;
       
    }
}