<?php

namespace App\Controller;

use App\Entity\Taxonomy;
use App\Form\TaxonomyType;
use App\Form\TaxonomyUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\TaxonomyRepository;
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
 * @Route("taxonomy", name="taxonomy_")
 */
class TaxonomyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TaxonomyRepository $taxonomyRepo): Response
    {
        $taxonomies =  $taxonomyRepo->findAll();
        $context = [
            'title' => 'Taxonomy List',
            'taxonomies' => $taxonomies
        ];
        return $this->render('taxonomy/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $taxonomy = new Taxonomy();
        $form = $this->createForm(TaxonomyType::class, $taxonomy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $taxonomy->setCreatedBy($this->getUser());
            }
            $taxonomy->setIsActive(true);
            $taxonomy->setCreatedAt(new \DateTime());
            $entmanager->persist($taxonomy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('taxonomy_index'));
        }

        $context = [
            'title' => 'Taxonomy Creation',
            'taxonomyForm' => $form->createView()
        ];
        return $this->render('taxonomy/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Taxonomy $taxonomySelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Taxonomy Details',
            'taxonomy' => $taxonomySelected
        ];
        return $this->render('taxonomy/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Taxonomy $taxonomy, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('taxonomy_edit', $taxonomy);
        $form = $this->createForm(TaxonomyUpdateType::class, $taxonomy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($taxonomy);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('taxonomy_index'));
        }

        $context = [
            'title' => 'Taxonomy Update',
            'taxonomyForm' => $form->createView()
        ];
        return $this->render('taxonomy/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Taxonomy $taxonomy, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($taxonomy->getId()) {
            $taxonomy->setIsActive(!$taxonomy->getIsActive());
        }
        $entmanager->persist($taxonomy);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $taxonomy->getIsActive()
        ], 200);
        //return $this->redirect($this->generateUrl('taxonomy_home'));
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
            $repoTaxonomy = $entmanager->getRepository(Taxonomy::class);
            // Query how many rows are there in the taxonomy table
            $totalTaxonomyBefore = $repoTaxonomy->createQueryBuilder('c')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(c.id)')
                ->getQuery()
                ->getSingleScalarResult();

            // Return a number as response
            // e.g 972

            // get the file (name from the taxonomyUploadFromExcelType form)
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
                $taxon_id = $row['A'];
                $genus = $row['B'];
                $species = $row['C'];
                $subtaxa = $row['D'];
                // check if the file doesn't have empty columns
                if ($taxon_id != null && $genus != null) {
                    // check if the data is upload in the database
                    $existingtaxonomy = $entmanager->getRepository(Taxonomy::class)->findOneBy(['taxonid' => $taxon_id]);
                    // upload data only for taxonomies that haven't been saved in the database
                    if (!$existingtaxonomy) {
                        $taxonomy = new Taxonomy();
                        if ($this->getUser()) {
                            $taxonomy->setCreatedBy($this->getUser());
                        }
                        $taxonomy->setTaxonId($taxon_id);
                        $taxonomy->setGenus($genus);
                        if ($subtaxa != null ) {
                            $taxonomy->setSubtaxa($subtaxa);
                        }
                        if ($species != null ) {
                            $taxonomy->setSpecies($species);
                        }
                        $taxonomy->setIsActive(true);
                        $taxonomy->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($taxonomy);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            //$entmanager->flush();
            // Query how many rows are there in the taxonomy table
            $totalTaxonomyAfter = $repoTaxonomy->createQueryBuilder('c')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(c.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalTaxonomyBefore == 0) {
                $this->addFlash('success', $totalTaxonomyAfter . " taxonomies have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalTaxonomyAfter - $totalTaxonomyBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new taxonomy have been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " taxonomy has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " taxonomies have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('taxonomy_index'));
        }

        $context = [
            'title' => 'Taxonomy Upload From Excel',
            'taxonomyUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('taxonomy/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function taxonomyTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/taxonomy_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'taxonomy_template_example.xls');
        return $response;
       
    }
}
