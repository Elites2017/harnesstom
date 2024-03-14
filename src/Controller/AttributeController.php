<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Entity\AttributeCategory;
use App\Form\AttributeType;
use App\Form\AttributeUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AttributeRepository;
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
 * @Route("/attribute", name="attribute_")
 */
class AttributeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeRepository $attributeRepo): Response
    {
        $attributes =  $attributeRepo->findAll();
        $context = [
            'title' => 'Attribute List',
            'attributes' => $attributes
        ];
        return $this->render('attribute/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $attribute = new Attribute();
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $attribute->setCreatedBy($this->getUser());
            }
            $attribute->setIsActive(true);
            $attribute->setCreatedAt(new \DateTime());
            $entmanager->persist($attribute);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('attribute_index'));
        }

        $context = [
            'title' => 'Attribute Creation',
            'attributeForm' => $form->createView()
        ];
        return $this->render('attribute/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Attribute $attributeSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Attribute Details',
            'attribute' => $attributeSelected
        ];
        return $this->render('attribute/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Attribute $attribute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('attribute_edit', $attribute);
        $form = $this->createForm(AttributeUpdateType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attribute);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_index'));
        }

        $context = [
            'title' => 'Attribute Update',
            'attributeForm' => $form->createView()
        ];
        return $this->render('attribute/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Attribute $attribute, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($attribute->getId()) {
            $attribute->setIsActive(!$attribute->getIsActive());
        }
        $entmanager->persist($attribute);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attribute->getIsActive()
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
            $repoAttribute = $entmanager->getRepository(Attribute::class);
            // Query how many rows are there in the Attribute table
            $totalAttributeBefore = $repoAttribute->createQueryBuilder('tab')
                // Filter by some Attribute if you want
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
                $attributeCat = $row['A'];
                $abbreviation = $row['B'];
                $attributeName = $row['C'];
                $description = $row['D'];
                $publicationRef = $row['E'];
                // check if the file doesn't have empty columns
                if ($attributeName != null && $attributeCat != null) {
                    // check if the data is upload in the database
                    $existingAttribute = $entmanager->getRepository(Attribute::class)->findOneBy(['name' => $attributeName]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingAttribute) {
                        $attribute = new Attribute();
                        if ($this->getUser()) {
                            $attribute->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $attributeCategory = $entmanager->getRepository(AttributeCategory::class)->findOneBy(['ontology_id' => $attributeCat]);
                            if (($attributeCategory != null) && ($attributeCategory instanceof \App\Entity\AttributeCategory)) {
                                $attribute->setCategory($attributeCategory);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the attribute category ontology " .$attributeCat);
                        }

                        try {
                            //code...
                            $attribute->setName($attributeName);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the attribute name " .$attributeName);
                        }

                        try {
                            //code...
                            $attribute->setAbbreviation($abbreviation);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the attribute abbreviation " .$abbreviation);
                        }

                        try {
                            //code...
                            $attribute->setDescription($description);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the attribute description " .$description);
                        }

                        $publicationRef = explode(",", $publicationRef);
                        
                        try {
                            //code...
                            $attribute->setPublicationReference($publicationRef);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the attribute publication Reference " .$publicationRef);
                        }

                        $attribute->setIsActive(true);
                        $attribute->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($attribute);
                            $entmanager->flush();
                        
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalAttributeAfter = $repoAttribute->createQueryBuilder('tab')
                // Filter by some Attribute if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAttributeBefore == 0) {
                $this->addFlash('success', $totalAttributeAfter . " attributes have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAttributeAfter - $totalAttributeBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Attribute has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " attribute has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " attributes have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('attribute_index'));
        }

        $context = [
            'title' => 'Attribute Upload From Excel',
            'attributeUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('attribute/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/attribute_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'attribute_template_example.xlsx');
        return $response;
       
    }
}
