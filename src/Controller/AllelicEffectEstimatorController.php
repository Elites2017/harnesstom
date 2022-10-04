<?php

namespace App\Controller;

use App\Entity\AllelicEffectEstimator;
use App\Form\AllelicEffectEstimatorType;
use App\Form\AllelicEffectEstimatorUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AllelicEffectEstimatorRepository;
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
 * @Route("/allelic/effect/estimator", name="allelic_effect_estimator_")
 */
class AllelicEffectEstimatorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AllelicEffectEstimatorRepository $allelicEffectEstimatorRepo): Response
    {
        $allelicEffectEstimators =  $allelicEffectEstimatorRepo->findAll();
        $context = [
            'title' => 'Allelic Effect Estimator',
            'allelicEffectEstimators' => $allelicEffectEstimators
        ];
        return $this->render('allelic_effect_estimator/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $allelicEffectEstimator = new AllelicEffectEstimator();
        $form = $this->createForm(AllelicEffectEstimatorType::class, $allelicEffectEstimator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $allelicEffectEstimator->setCreatedBy($this->getUser());
            }
            $allelicEffectEstimator->setIsActive(true);
            $allelicEffectEstimator->setCreatedAt(new \DateTime());
            $entmanager->persist($allelicEffectEstimator);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('allelic_effect_estimator_index'));
        }

        $context = [
            'title' => 'Allelic Effect Estimator Creation',
            'allelicEffectEstimatorForm' => $form->createView()
        ];
        return $this->render('allelic_effect_estimator/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AllelicEffectEstimator $allelicEffectEstimatorselected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Allelic Effect Estimator Details',
            'allelicEffectEstimator' => $allelicEffectEstimatorselected
        ];
        return $this->render('allelic_effect_estimator/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AllelicEffectEstimator $allelicEffectEstimator, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('allelic_effect_estimator_edit', $allelicEffectEstimator);
        $form = $this->createForm(AllelicEffectEstimatorUpdateType::class, $allelicEffectEstimator);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($allelicEffectEstimator);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('allelic_effect_estimator_index'));
        }

        $context = [
            'title' => 'Allelic Effect Estimator Update',
            'allelicEffectEstimatorForm' => $form->createView()
        ];
        return $this->render('allelic_effect_estimator/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(AllelicEffectEstimator $allelicEffectEstimator, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($allelicEffectEstimator->getId()) {
            $allelicEffectEstimator->setIsActive(!$allelicEffectEstimator->getIsActive());
        }
        $entmanager->persist($allelicEffectEstimator);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $allelicEffectEstimator->getIsActive()
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
            $repoAllelicEffectEstimator = $entmanager->getRepository(AllelicEffectEstimator::class);
            // Query how many rows are there in the Country table
            $totalAllelicEEBefore = $repoAllelicEffectEstimator->createQueryBuilder('a')
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
                $name = $row['B'];
                $description = $row['C'];
                $ontologyId = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($name != null) {
                    // check if the data has already been uploaded in the database
                    $existingAlleclicEE = $entmanager->getRepository(AllelicEffectEstimator::class)->findOneBy(['ontology_id' => $ontologyId]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingAlleclicEE) {
                        $allelicEffectEstimator = new AllelicEffectEstimator();
                        // set the parent term based on it's ontology ID generated by the database
                        // use the received parentTerm to search its ontologyID as it is a self reflecting / relationship table
                        $aEEOntologyIdParentTerm = $entmanager->getRepository(AllelicEffectEstimator::class)->findOneBy(['ontology_id' => $parentTerm]);
                        if (($aEEOntologyIdParentTerm != null) && ($aEEOntologyIdParentTerm instanceof \App\Entity\AllelicEffectEstimator)) {
                            $allelicEffectEstimator->setParentTerm($aEEOntologyIdParentTerm);
                        }
                        if ($this->getUser()) {
                            $allelicEffectEstimator->setCreatedBy($this->getUser());
                        }
                        $allelicEffectEstimator->setName($name);
                        $allelicEffectEstimator->setOntologyId($ontologyId);
                        $allelicEffectEstimator->setDescription($description);
                        $allelicEffectEstimator->setIsActive(true);
                        $allelicEffectEstimator->setCreatedAt(new \DateTime());
                        $entmanager->persist($allelicEffectEstimator);
                    }
                } else {
                    $this->addFlash('danger', "Check your file again, Name and ontology_id must be filled / provide");
                }
            }
            $entmanager->flush();
            // Query how many rows are there in the allelicEffectEstimator table
            $totalAllelicEEAfter = $repoAllelicEffectEstimator->createQueryBuilder('a')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAllelicEEBefore == 0) {
                $this->addFlash('success', $totalAllelicEEAfter . " allelic effest estimator have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAllelicEEAfter - $totalAllelicEEBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new allelic effect estimator has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " allelic effect estimator has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " allelic effect estimators have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('allelic_effect_estimator_index'));
        }

        $context = [
            'title' => 'Allelic Effect Estimator Upload From Excel',
            'allelicEffectEstimatorUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('allelic_effect_estimator/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function allelicEffectEstimatorTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/allelic_ee_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'allelic_ee_template_example.xls');
        return $response;
       
    }
}
