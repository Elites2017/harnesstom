<?php

namespace App\Controller;

use App\Entity\Scale;
use App\Entity\ScaleCategory;
use App\Form\ScaleCategoryType;
use App\Form\ScaleCategoryUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ScaleCategoryRepository;
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
 * @Route("scale/category", name="scale_category_")
 */
class ScaleCategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ScaleCategoryRepository $scaleCategoryRepo): Response
    {
        $scaleCategories =  $scaleCategoryRepo->findAll();
        $context = [
            'title' => 'Scale Category List',
            'scaleCategories' => $scaleCategories
        ];
        return $this->render('scale_category/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $scaleCategory = new ScaleCategory();
        $form = $this->createForm(ScaleCategoryType::class, $scaleCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $scaleCategory->setCreatedBy($this->getUser());
            }
            $scaleCategory->setIsActive(true);
            $scaleCategory->setCreatedAt(new \DateTime());
            $entmanager->persist($scaleCategory);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('scale_category_index'));
        }

        $context = [
            'title' => 'Scale Category Creation',
            'scaleCategoryForm' => $form->createView()
        ];
        return $this->render('scale_category/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(ScaleCategory $scaleCategorySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Scale Category Details',
            'scaleCategory' => $scaleCategorySelected
        ];
        return $this->render('scale_category/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(ScaleCategory $scaleCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('scale_category_edit', $scaleCategory);
        $form = $this->createForm(ScaleCategoryUpdateType::class, $scaleCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($scaleCategory);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly updated");
            return $this->redirect($this->generateUrl('scale_category_index'));
        }

        $context = [
            'title' => 'Scale Category Update',
            'scaleCategoryForm' => $form->createView()
        ];
        return $this->render('scale_category/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(ScaleCategory $scaleCategory, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($scaleCategory->getId()) {
            $scaleCategory->setIsActive(!$scaleCategory->getIsActive());
        }
        $entmanager->persist($scaleCategory);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $scaleCategory->getIsActive()
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
            $repoScaleCategory = $entmanager->getRepository(ScaleCategory::class);
            // Query how many rows are there in the ScaleCategory table
            $totalScaleCategoryBefore = $repoScaleCategory->createQueryBuilder('tab')
                // Filter by some ScaleCategory if you want
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
                $scale = $row['A'];
                $label = $row['B'];
                $score = $row['C'];
                $min = $row['D'];
                $max = $row['E'];
                // check if the file doesn't have empty columns
                if ($scale != null && $label != null) {
                    try {
                        //code...
                        $scaleCategoryScale = $entmanager->getRepository(Scale::class)->findOneBy(['name' => $scale]);
                    } catch (\Throwable $th) {
                        //throw $th;
                        $this->addFlash('danger', " there is a problem with the scale name, can not create scale category " .$scale);
                    }
                    if ($scaleCategoryScale) {
                        // check if the data is upload in the database
                        $existingScaleCategory = $entmanager->getRepository(ScaleCategory::class)->findOneBy(['scale' => $scaleCategoryScale, 'label' => $label]);
                        // upload data only for objects that haven't been saved in the database
                        if (!$existingScaleCategory) {
                            $scaleCategory = new ScaleCategory();
                            if ($this->getUser()) {
                                $scaleCategory->setCreatedBy($this->getUser());
                            }

                            try {
                                //code...
                                $scaleCategory->setLabel($label);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the scale category label " .$label);
                            }

                            try {
                                //code...
                                $scaleCategory->setScore($score);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the scale category score " .$score);
                            }

                            try {
                                //code...
                                $scaleCategory->setScale($scaleCategoryScale);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the scale scale " .$scale);
                            }

                            try {
                                //code...
                                $scaleCategory->setMin($min);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the scale category min value " .$min. " " .strtoupper($th->getMessage()));
                            }

                            try {
                                //code...
                                $scaleCategory->setMax($max);
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', " there is a problem with the scale category max value " .$max. " " .strtoupper($th->getMessage()));
                            }

                            $scaleCategory->setIsActive(true);
                            $scaleCategory->setCreatedAt(new \DateTime());
                            try {
                                //code...
                                $entmanager->persist($scaleCategory);
                                $entmanager->flush();
                            
                            } catch (\Throwable $th) {
                                //throw $th;
                                $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                            }
                        }
                    } else {
                        $this->addFlash('danger', " there is no scale ");
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalScaleCategoryAfter = $repoScaleCategory->createQueryBuilder('tab')
                // Filter by some ScaleCategory if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalScaleCategoryBefore == 0) {
                $this->addFlash('success', $totalScaleCategoryAfter . " scale categories have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalScaleCategoryAfter - $totalScaleCategoryBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new scale category has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " scale category has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " scale categories have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('scale_category_index'));
        }

        $context = [
            'title' => 'Scale Category Upload From Excel',
            'scaleCategoryUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('scale_category/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/scale_category_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'scale_category_template_example.xlsx');
        return $response;
       
    }
}
