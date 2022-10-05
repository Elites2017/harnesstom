<?php

/* 
    This is the SeasonController which contains the CRUD method of this object.
    1. The index function is to list all the object from the DB
    
    2. The create function is to create the object by
        2.1 initializes the object
        2.2 create the form from the SeasonType form and do the binding
        2.2.1 pass the request to the form to handle it
        2.2.2 Analyze the form, if everything is okay, save the object and redirect the user
        if there is any problem, the same page will be display to the user with the context
    
    3. The details function is just to show the details of the selected object to the user.

    4. the update funtion is a little bit similar with the create one, because they almost to the same thing, but
    in the update, we don't initialize the object as it will come from the injection and it is supposed to be existed.

    5. the delete function is to delete the object from the DB, but to keep a trace, it is preferable
    to just change the state of the object.

    March 11, 2022
    David PIERRE
*/


namespace App\Controller;

use App\Entity\Season;
use App\Entity\User;
use DateTime;
use App\Form\SeasonType;
use App\Form\SeasonUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\SeasonRepository;
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
 * @Route("/season", name="season_")
 */
class SeasonController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SeasonRepository $seasonRepo): Response
    {
        $seasons =  $seasonRepo->findAll();
        $context = [
            'title' => 'Seasons',
            'seasons' => $seasons
        ];
        return $this->render('season/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $season->setCreatedBy($this->getUser());
            }
            $season->setIsActive(true);
            $season->setCreatedAt(new \DateTime());
            $entmanager->persist($season);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('season_index'));
        }

        $context = [
            'title' => 'Season Creation',
            'seasonForm' => $form->createView()
        ];
        return $this->render('season/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Season $seasonSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Season Details',
            'season' => $seasonSelected
        ];
        return $this->render('season/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Season $season, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('season_edit', $season);
        $form = $this->createForm(SeasonUpdateType::class, $season);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($season);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('season_index'));
        }

        $context = [
            'title' => 'Season Update',
            'seasonForm' => $form->createView()
        ];
        return $this->render('season/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Season $season, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($season->getId()) {
            $season->setIsActive(!$season->getIsActive());
        }
        $entmanager->persist($season);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $season->getIsActive()
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
            $repoSeason = $entmanager->getRepository(Season::class);
            // Query how many rows are there in the Country table
            $totalSeasonBefore = $repoSeason->createQueryBuilder('a')
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
                $ontology_id = $row['A'];
                $name = $row['B'];
                $description = $row['C'];
                $parentTermString = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $name != null) {
                    // check if the data is upload in the database
                    $existingSeason = $entmanager->getRepository(Season::class)->findOneBy(['ontology_id' => $ontology_id]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingSeason) {
                        $season = new Season();
                        if ($this->getUser()) {
                            $season->setCreatedBy($this->getUser());
                        }
                        $season->setOntologyId($ontology_id);
                        $season->setName($name);
                        if ($description != null) {
                            $season->setDescription($description);
                        }
                        if ($parentTermString != null) {
                            $season->setParOnt($parentTermString);
                        }
                        $season->setIsActive(true);
                        $season->setCreatedAt(new \DateTime());
                        $entmanager->persist($season);
                    }
                }
            }
            $entmanager->flush();
            // get the connection
            $connexion = $entmanager->getConnection();
            // another flush because of self relationship. The ontology ID needs to be stored in the db first before it can be accessed for the parent term
            foreach ($sheetData as $key => $row) {
                $ontology_id = $row['A'];
                $parentTerm = $row['D'];
                // check if the file doesn't have empty columns
                if ($ontology_id != null && $parentTerm != null ) {
                    // check if the data is upload in the database
                    $ontologyIdParentTerm = $entmanager->getRepository(Season::class)->findOneBy(['ontology_id' => $parentTerm]);
                    if (($ontologyIdParentTerm != null) && ($ontologyIdParentTerm instanceof \App\Entity\Season)) {
                        $ontId = $ontologyIdParentTerm->getId();
                        // get the real string (parOnt) parent term or its line id so that to do the link 
                        $stringParentTerm = $entmanager->getRepository(Season::class)->findOneBy(['par_ont' => $parentTerm, 'is_poau' => null]);
                        $parentTermId = $stringParentTerm->getId();
                        // update the is_poau (Is Parent Term Ontology ID Already Updated) so that it doesn't keep updating the same row in case of same parent term
                        $res = $connexion->executeStatement('UPDATE season SET parent_term_id = ?, is_poau = ? WHERE id = ?', [$ontId, 1, $parentTermId]);
                    }
                }
            }

            // Query how many rows are there in the Season table
            $totalSeasonAfter = $repoSeason->createQueryBuilder('a')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalSeasonBefore == 0) {
                $this->addFlash('success', $totalSeasonAfter . " season have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalSeasonAfter - $totalSeasonBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new season has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " season has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " seasons have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('season_index'));
        }

        $context = [
            'title' => 'Season Upload From Excel',
            'seasonUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('season/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function SeasonTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/season_template_example.xls');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'season_template_example.xls');
        return $response;
       
    }
}
