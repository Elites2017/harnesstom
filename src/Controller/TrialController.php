<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Trial;
use App\Entity\SharedWith;
use App\Entity\TrialType as EntityTrialType;
use App\Form\TrialType;
use App\Form\TrialUpType;
use App\Form\UploadFromExcelType;
use App\Repository\UserRepository;
use App\Repository\SharedWithRepository;
use App\Repository\TrialRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

// set a class level route
/**
 * @Route("/trial", name="trial_")
 */
class TrialController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TrialRepository $trialRepo): Response
    {
        $trials = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res != false) {
                $trials = $trialRepo->findAll();
            } else {
                $trials = $trialRepo->findReleasedTrials($this->getUser());
            }
        } else {
            $trials = $trialRepo->findReleasedTrials();
        }
        
        $context = [
            'title' => 'Trial List',
            'trials' => $trials
        ];
        return $this->render('trial/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trial = new Trial();
        $form = $this->createForm(TrialType::class, $trial);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $trial->setCreatedBy($this->getUser());
            }
            $trial->setIsActive(true);
            $trial->setCreatedAt(new \DateTime());
            $entmanager->persist($trial);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Creation',
            'trialForm' => $form->createView()
        ];
        return $this->render('trial/create.html.twig', $context);
    }

    /**
     * @Route("/{id}/share/with", name="share_with")
     */
    public function shareWith(Request $request, EntityManagerInterface $entmanager, Trial $trialSelected, UserRepository $userRepo, SharedWithRepository $sharedWithRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $users = $userRepo->getUserShareableTrial($this->getUser());
        // prevent the user from sharing other users' trials
        if ($trialSelected->getCreatedBy() == $this->getUser()) {
            // check if the an action has been performed on the button
            // if so, a parameter is added to the url
            if ($request->query->get("userId")) {
                $userId = $request->query->get("userId");
                $user = $userRepo->findOneById($userId);
                // check if this trial has already been shared with this user
                $existedSW = $sharedWithRepo->findOneBy(["trial" => $trialSelected, "user" => $user]);
                if ($existedSW) {
                    $entmanager->remove($existedSW);
                    $entmanager->flush();
                    // json reponse
                    $response = [
                        "code"=> "200",
                        "message"=> "Share With"
                    ];
                    $returnResponse = new JsonResponse($response);
                    return $returnResponse;
                } else {
                    // create the shared with object
                    $sharedWith = new SharedWith();
                    $sharedWith->setTrial($trialSelected);
                    $sharedWith->setUser($user);
                    $sharedWith->setIsActive(true);
                    $sharedWith->setCreatedAt(new \DateTime());
                    $sharedWith->setCreatedBy($this->getUser());
                    $entmanager->persist($sharedWith);
                    $entmanager->flush();
                    // Json reponse response
                    $response = [
                        "code"=> "200",
                        "message"=> "UnShare With"
                    ];
                    $returnResponse = new JsonResponse($response);
                    return $returnResponse;
                }
            }
        } else {
            $this->addFlash('danger', "You are not allowed to share the trial of another user");
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Details',
            'trial' => $trialSelected,
            'users' => $users
        ];
        return $this->render('trial/share_with.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Trial $trialSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Trial Details',
            'trial' => $trialSelected
        ];
        return $this->render('trial/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Trial $trial, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('trial_edit', $trial);
        $form = $this->createForm(TrialUpType::class, $trial);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($trial);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Update',
            'trialForm' => $form->createView()
        ];
        return $this->render('trial/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Trial $trial, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($trial->getId()) {
            $trial->setIsActive(!$trial->getIsActive());
        }
        $entmanager->persist($trial);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $trial->getIsActive()
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
            $repoTrial = $entmanager->getRepository(Trial::class);
            // Query how many rows are there in the trial table
            $totalTrialBefore = $repoTrial->createQueryBuilder('tab')
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
                $programAbbreviation = $row['A'];
                $trialAbbreviation = $row['B'];
                $trialName = $row['C'];
                $trialDescription = $row['D'];
                $ontIdTrialType = $row['E'];
                $trialPUI = $row['F'];
                $startDate = $row['G'];
                $endDate = $row['H'];
                $publicReleaseDate = $row['I'];
                $licence = $row['J'];
                $publicationRef = $row['K'];
                // check if the file doesn't have empty columns
                if ($trialAbbreviation != null && $trialName != null) {
                    // check if the data is upload in the database
                    $existingTrial = $entmanager->getRepository(Trial::class)->findOneBy(['abbreviation' => $trialAbbreviation]);
                    // upload data only for countries that haven't been saved in the database
                    if (!$existingTrial) {
                        $trial = new Trial();
                        if ($this->getUser()) {
                            $trial->setCreatedBy($this->getUser());
                        }
                        $trialProgram = $entmanager->getRepository(Program::class)->findOneBy(['abbreviation' => $programAbbreviation]);
                        if (($trialProgram != null) && ($trialProgram instanceof \App\Entity\Program)) {
                            $trial->setProgram($trialProgram);
                        }
                        $trialType = $entmanager->getRepository(EntityTrialType::class)->findOneBy(['ontology_id' => $ontIdTrialType]);
                        if (($trialType != null) && ($trialType instanceof \App\Entity\TrialType)) {
                            $trial->setTrialType($trialType);
                        }
                        $trial->setDescription($trialDescription);
                        if ($startDate !=null) {
                            $trial->setStartDate(\DateTime::createFromFormat('Y-m-d', $startDate));
                        }
                        if ($endDate !=null) {
                            $trial->setEndDate(\DateTime::createFromFormat('Y-m-d', $endDate));
                        }
                        if ($publicReleaseDate !=null) {
                            $trial->setPublicReleaseDate(\DateTime::createFromFormat('Y-m-d', $publicReleaseDate));
                        }
                        $trial->setName($trialName);
                        $trial->setPui($trialPUI);
                        $trial->setAbbreviation($trialAbbreviation);
                        $publicationRef = explode(",", $publicationRef);
                        $trial->setPublicationReference($publicationRef);
                        $trial->setLicense($licence);
                        $trial->setIsActive(true);
                        $trial->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($trial);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // Query how many rows are there in the table
            $totalTrialAfter = $repoTrial->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalTrialBefore == 0) {
                $this->addFlash('success', $totalTrialAfter . " trial have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalTrialAfter - $totalTrialBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new trial has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " trial has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " trial have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('trial_index'));
        }

        $context = [
            'title' => 'Trial Upload From Excel',
            'trialUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('trial/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/trial_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'trial_template_example.xlsx');
        return $response;
       
    }
}



