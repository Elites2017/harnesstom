<?php

namespace App\Controller;

use App\Entity\Crop;
use App\Repository\CropRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CropType;
use App\Form\CropUpdateType;
use App\Form\UploadFromExcelType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/crop", name="crop_")
 */
class CropController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CropRepository $cropRepo): Response
    {
        $crops =  $cropRepo->findAll();
        $context = [
            'title' => 'Crops',
            'crops' => $crops
        ];
        return $this->render('crop/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $crop = new Crop();
        $form = $this->createForm(cropType::class, $crop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $crop->setCreatedBy($this->getUser());
            }
            $crop->setIsActive(true);
            $crop->setCreatedAt(new \DateTime());
            $entmanager->persist($crop);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('crop_index'));
        }

        $context = [
            'title' => 'Crop Creation',
            'cropForm' => $form->createView()
        ];
        return $this->render('crop/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Crop $cropSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Crop Details',
            'crop' => $cropSelected
        ];
        return $this->render('crop/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Crop $crop, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('crop_edit', $crop);
        $form = $this->createForm(CropUpdateType::class, $crop);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($crop);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('crop_index'));
        }

        $context = [
            'title' => 'Crop Update',
            'cropForm' => $form->createView()
        ];
        return $this->render('crop/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Crop $crop, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($crop->getId()) {
            $crop->setIsActive(!$crop->getIsActive());
        }
        $entmanager->persist($crop);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $crop->getIsActive()
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
            $repoCrop = $entmanager->getRepository(Crop::class);
            // Query how many rows are there in the Crop table
            $totalCropBefore = $repoCrop->createQueryBuilder('tab')
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
                $commonCropName = $row['A'];
                // check if the file doesn't have empty columns
                if ($commonCropName != null) {
                    // check if the data is upload in the database
                    $existingCrop = $entmanager->getRepository(Crop::class)->findOneBy(['commonCropName' => $commonCropName]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingCrop) {
                        $crop = new Crop();
                        if ($this->getUser()) {
                            $crop->setCreatedBy($this->getUser());
                        }
                        $crop->setCommonCropName($commonCropName);
                        $crop->setIsActive(true);
                        $crop->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($crop);
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
            $totalCropAfter = $repoCrop->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalCropBefore == 0) {
                $this->addFlash('success', $totalCropAfter . " crops have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalCropAfter - $totalCropBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new crop has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " crop has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " crops have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('crop_index'));
        }

        $context = [
            'title' => 'Crop Upload From Excel',
            'cropUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('crop/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/crop_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'crop_template_example.xls');
        return $response;
       
    }
}
