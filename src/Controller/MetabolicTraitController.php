<?php

namespace App\Controller;

use App\Entity\MetabolicTrait;
use App\Form\MetabolicTraitType;
use App\Form\MetabolicTraitUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\MetabolicTraitRepository;
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
 * @Route("/metabolic/trait", name="metabolic_trait_")
 */
class MetabolicTraitController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(MetabolicTraitRepository $metabolicTraitRepo): Response
    {
        $metabolicTraits =  $metabolicTraitRepo->findAll();
        $context = [
            'title' => 'Metabolic Trait List',
            'metabolicTraits' => $metabolicTraits
        ];
        return $this->render('metabolic_trait/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $metabolicTrait = new MetabolicTrait();
        $form = $this->createForm(MetabolicTraitType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $metabolicTrait->setCreatedBy($this->getUser());
            }
            $metabolicTrait->setIsActive(true);
            $metabolicTrait->setCreatedAt(new \DateTime());
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Creation',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(MetabolicTrait $metabolicTraitSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Metabolic Trait Details',
            'metabolicTrait' => $metabolicTraitSelected
        ];
        return $this->render('metabolic_trait/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('metabolic_trait_edit', $metabolicTrait);
        $form = $this->createForm(MetabolicTraitUpdateType::class, $metabolicTrait);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($metabolicTrait);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Update',
            'metabolicTraitForm' => $form->createView()
        ];
        return $this->render('metabolic_trait/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(MetabolicTrait $metabolicTrait, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($metabolicTrait->getId()) {
            $metabolicTrait->setIsActive(!$metabolicTrait->getIsActive());
        }
        $entmanager->persist($metabolicTrait);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $metabolicTrait->getIsActive()
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
            $repometabolicTrait = $entmanager->getRepository(metabolicTrait::class);
            // Query how many rows are there in the metabolicTrait table
            $totalmetabolicTraitBefore = $repometabolicTrait->createQueryBuilder('tab')
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
                    $existingmetabolicTrait = $entmanager->getRepository(metabolicTrait::class)->findOneBy(['name' => $name]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingmetabolicTrait) {
                        $metabolicTrait = new metabolicTrait();
                        if ($this->getUser()) {
                            $metabolicTrait->setCreatedBy($this->getUser());
                        }
                        $metabolicTrait->setName($name);
                        $metabolicTrait->setOntologyId($ontology_id);
                        //$metabolicTrait->setParentTerm($parentTerm);
                        $metabolicTrait->setIsActive(true);
                        $metabolicTrait->setCreatedAt(new \DateTime());
                        $entmanager->persist($metabolicTrait);
                    }
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the Country table
            $totalmetabolicTraitAfter = $repometabolicTrait->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalmetabolicTraitBefore == 0) {
                $this->addFlash('success', $totalmetabolicTraitAfter . " metabolic traits have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalmetabolicTraitAfter - $totalmetabolicTraitBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new metabolic trait has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolic trait has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " metabolic traits have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('metabolic_trait_index'));
        }

        $context = [
            'title' => 'Metabolic Trait Upload From Excel',
            'metabolicTraitUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('metabolicTrait/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/metabolic_trait_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'metabolic_trait_template_example.xls');
        return $response;
       
    }
}

