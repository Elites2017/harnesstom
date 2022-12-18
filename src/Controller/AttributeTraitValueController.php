<?php

namespace App\Controller;

use App\Entity\Accession;
use App\Entity\Attribute;
use App\Entity\AttributeTraitValue;
use App\Entity\TraitClass;
use App\Form\AttributeTraitValueType;
use App\Form\AttributeTraitValueUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\AttributeTraitValueRepository;
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
 * @Route("/attribute/trait/value", name="attribute_trait_value_")
 */
class AttributeTraitValueController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(AttributeTraitValueRepository $attributeTraitValueRepo): Response
    {
        $attributeTraitValues =  $attributeTraitValueRepo->findAll();
        $context = [
            'title' => 'Attribute Trait Value List',
            'attributeTraitValues' => $attributeTraitValues
        ];
        return $this->render('attribute_trait_value/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $attributeTraitValue = new AttributeTraitValue();
        $form = $this->createForm(AttributeTraitValueType::class, $attributeTraitValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                    $attributeTraitValue->setCreatedBy($this->getUser());
                }
            $attributeTraitValue->setIsActive(true);
            $attributeTraitValue->setCreatedAt(new \DateTime());
            $entmanager->persist($attributeTraitValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_trait_value_index'));
        }

        $context = [
            'title' => 'Attribute Trait Value Creation',
            'attributeTraitValueForm' => $form->createView()
        ];
        return $this->render('attribute_trait_value/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(AttributeTraitValue $attributeTraitValueSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Attribute Trait Value Details',
            'attributeTraitValue' => $attributeTraitValueSelected
        ];
        return $this->render('attribute_trait_value/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(AttributeTraitValue $attributeTraitValue, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('attribute_trait_value_edit', $attributeTraitValue);
        $form = $this->createForm(AttributeTraitValueUpdateType::class, $attributeTraitValue);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($attributeTraitValue);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('attribute_trait_value_index'));
        }

        $context = [
            'title' => 'Attribute Trait Value Update',
            'attributeTraitValueForm' => $form->createView()
        ];
        return $this->render('attribute_trait_value/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(AttributeTraitValue $attributeTraitValue, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($attributeTraitValue->getId()) {
            $attributeTraitValue->setIsActive(!$attributeTraitValue->getIsActive());
        }
        $entmanager->persist($attributeTraitValue);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $attributeTraitValue->getIsActive()
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
            $repoAttributeTraitValue = $entmanager->getRepository(AttributeTraitValue::class);
            // Query how many rows are there in the Attribute trait value table
            $totalAttributeTraitValueBefore = $repoAttributeTraitValue->createQueryBuilder('tab')
                // Filter by some Attribute trait value if you want
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
                $attributeTraitValueAcc = $row['A'];
                $attributeName = $row['B'];
                $atributeTraitvalueTrait = $row['C'];
                $value = $row['D'];
                $publicationRef = $row['E'];
                // check if the file doesn't have empty columns
                if ($attributeName != null && $attributeTraitValueAcc != null) {
                    // check if the data is upload in the database
                    try {
                        //code...
                        $attributeTraitValueAccession = $entmanager->getRepository(Accession::class)->findOneBy(['accenumb' => $attributeTraitValueAcc]);
                        $attributeTraitValueAttr = $entmanager->getRepository(Attribute::class)->findOneBy(['name' => $attributeName]);
                        if (($attributeTraitValueAccession != null) && ($attributeTraitValueAttr != null))  {
                            $existingAttributeTraitValue = $entmanager->getRepository(AttributeTraitValue::class)->findOneBy(
                                ['attribute' => $attributeTraitValueAttr, 'accession' => $attributeTraitValueAccession]);
                            // upload data only for objects that haven't been saved in the database
                            if (!$existingAttributeTraitValue) {
                                $attributeTraitValue = new AttributeTraitValue();
                                if ($this->getUser()) {
                                    $attributeTraitValue->setCreatedBy($this->getUser());
                                }
                                try {
                                    //code...
                                    $attributeTraitValue->setAccession($attributeTraitValueAccession);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the attribute trait value accession number " .$attributeTraitValueAcc);
                                }

                                try {
                                    //code...
                                    $attributeTraitValue->setAttribute($attributeTraitValueAttr);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the attribute trait value attribute name " .$attributeName);
                                }

                                try {
                                    //code...
                                    $attrTraitValueTrait = $entmanager->getRepository(TraitClass::class)->findOneBy(['ontology_id' => $atributeTraitvalueTrait]);
                                    if (($attrTraitValueTrait != null) && ($attrTraitValueTrait instanceof \App\Entity\TraitClass)) {
                                        $attributeTraitValue->setTrait($attrTraitValueTrait);
                                    }
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the attribute trait value attribute ontology id " .$atributeTraitvalueTrait);
                                }

                                try {
                                    //code...
                                    $attributeTraitValue->setValue($value);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the attribute trait value value " .$value);
                                }

                                $publicationRef = explode(",", $publicationRef);
                                
                                try {
                                    //code...
                                    $attributeTraitValue->setPublicationReference($publicationRef);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', " there is a problem with the attribute trait value publication Reference " .$publicationRef);
                                }

                                $attributeTraitValue->setIsActive(true);
                                $attributeTraitValue->setCreatedAt(new \DateTime());
                                try {
                                    //code...
                                    $entmanager->persist($attributeTraitValue);
                                    $entmanager->flush();
                                
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                                }
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        $this->addFlash('danger', " can not check if the couple accession " .$attributeTraitValueAcc. " and the attribute name ". $attributeName ." has been already used in the database");
                    }
                }
            }
            
            // Query how many rows are there in the table
            $totalAttributeTraitValueAfter = $repoAttributeTraitValue->createQueryBuilder('tab')
                // Filter by some Attribute trait value if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalAttributeTraitValueBefore == 0) {
                $this->addFlash('success', $totalAttributeTraitValueAfter . " attribute trait values have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalAttributeTraitValueAfter - $totalAttributeTraitValueBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Attribute trait value has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " attribute trait value has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " attribute trait values have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('attribute_trait_value_index'));
        }

        $context = [
            'title' => 'Attribute Trait Value Upload From Excel',
            'attributeTraitValueUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('attribute_trait_value/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/attribute_trait_value_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'attribute_trait_value_template_example.xlsx');
        return $response;
       
    }
}
