<?php

namespace App\Controller;

use App\Entity\ExperimentalDesignType;
use App\Entity\FactorType;
use App\Entity\GrowthFacilityType;
use App\Entity\Institute;
use App\Entity\Location;
use App\Entity\ParameterValue;
use App\Entity\Season;
use App\Entity\Study;
use App\Entity\Trial;
use App\Form\StudyType;
use App\Form\StudyUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\StudyRepository;
use App\Repository\TrialRepository;
use App\Service\Datatable;
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
 * @Route("/study", name="study_")
 */
class StudyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(StudyRepository $studyRepo): Response
    {
        $studies = [];
        if($this->getUser()) {
            $userRoles = $this->getUser()->getRoles();
            $adm = "ROLE_ADMIN";
            $res = array_search($adm, $userRoles);
            if ($res !== false) {
                $studies = $studyRepo->findAll();
            } else {
                $studies = $studyRepo->findReleasedTrialStudy($this->getUser());
            }
        } else {
            $studies = $studyRepo->findReleasedTrialStudy();
        }
        $context = [
            'title' => 'Study List',
            'studies' => $studies
        ];
        return $this->render('study/index.html.twig', $context);
    }

    /**
     * @Route("/datatable", name="datatable")
     */
    public function datatable(Datatable $datatableService, StudyRepository $studyRepo, Request $request)
    {
        $datatableRes = $datatableService->getDatatable($studyRepo, $request);
        return $datatableRes;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $study = new Study();
        $form = $this->createForm(StudyType::class, $study);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();
            $parameterValues = $form->get('extra')->getData();
            if ($startDate > $endDate) {
                $this->addFlash('danger', "The end date must be greater than the start date");
            } else {
                if ($this->getUser()) {
                    $study->setCreatedBy($this->getUser());
                }
                $study->setIsActive(true);
                $study->setCreatedAt(new \DateTime());
                $entmanager->persist($study);
                // study parameter value
                if ($parameterValues) {
                    foreach ($parameterValues as $key => $parameterValue) {
                        if ($parameterValue === null) {
                            $this->addFlash('danger', "The paramater and its value can not be empty, you must fill / provide them");
                        } else {
                            if ($parameterValue->getValue() === null) {
                                $this->addFlash('danger', "The value of the parameter can not be empty, you must fill / provide it");
                            } else if ($parameterValue->getParameter() === null) {
                                $this->addFlash('danger', "The parameter can not be empty, you must fill / provide it");
                            } else {
                                # code...
                                $entmanager->persist($parameterValue);
                                $study->addParameterValue($parameterValue);
                                $entmanager->flush();
                                $this->addFlash('success', " One study has been successfuly added");
                                return $this->redirect($this->generateUrl('study_index'));
                            }
                        }
                    }
                } else {
                    $entmanager->flush();
                    $this->addFlash('success', " One study has been successfuly added");
                    return $this->redirect($this->generateUrl('study_index'));
                }
            }
        }

        $context = [
            'title' => 'Study Creation',
            'studyForm' => $form->createView()
        ];
        return $this->render('study/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Study $studieSelected, TrialRepository $trialRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // test if the trial is accessible to this user in order to show the trial study
        if ($trialRepo->isAccessible($this->getUser(), $studieSelected->getTrial())) {
            $context = [
                'title' => 'Study Details',
                'study' => $studieSelected
            ];
            return $this->render('study/details.html.twig', $context);
        } else {
            $this->addFlash('danger', "You are not allowed to see a study that is private and not shared with you");
            return $this->redirect($this->generateUrl('study_index'));
        }
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Study $study, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('study_edit', $study);
        $form = $this->createForm(StudyUpdateType::class, $study);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $form->get('startDate')->getData();
            $endDate = $form->get('endDate')->getData();
            $parameterValues = $form->get('extra')->getData();
            if ($startDate > $endDate) {
                $this->addFlash('danger', "The end date must be greater than the start date");
            } else {
                $entmanager->persist($study);
                // study parameter value
                if ($parameterValues) {
                    foreach ($parameterValues as $key => $parameterValue) {
                        if ($parameterValue === null) {
                            $this->addFlash('danger', "The paramater and its value can not be empty, you must fill / provide them");
                        } else {
                            if ($parameterValue->getValue() === null) {
                                $this->addFlash('danger', "The value of the parameter can not be empty, you must fill / provide it");
                            } else if ($parameterValue->getParameter() === null) {
                                $this->addFlash('danger', "The parameter can not be empty, you must fill / provide it");
                            } else {
                                # code...
                                $entmanager->persist($parameterValue);
                                $study->addParameterValue($parameterValue);
                                $entmanager->flush();
                                $this->addFlash('success', " One study has been successfuly added");
                                return $this->redirect($this->generateUrl('study_index'));
                            }
                        }
                    }
                } else {
                    $entmanager->flush();
                    $this->addFlash('success', " One study has been successfuly added");
                    return $this->redirect($this->generateUrl('study_index'));
                }
            }
        }

        $context = [
            'title' => 'Study Update',
            'studyForm' => $form->createView()
        ];
        return $this->render('study/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(Study $study, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($study->getId()) {
            $study->setIsActive(!$study->getIsActive());
        }
        $entmanager->persist($study);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $study->getIsActive()
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
            $repoStudy = $entmanager->getRepository(Study::class);
            // Query how many rows are there in the Study table
            $totalStudyBefore = $repoStudy->createQueryBuilder('tab')
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
                $trialAbbreviation = $row['A'];
                $studyAbbreviation = $row['B'];
                $studyName = $row['C'];
                $studyDescription = $row['D'];
                $factorOntologyId = $row['E'];
                $season = $row['F'];
                $startDate = $row['G'];
                $endDate = $row['H'];
                $instituteId = $row['I'];
                $locationAbbreviation = $row['J'];
                $growthFacilityType = $row['K'];
                $growthFacilityDesc = $row['L'];
                $culturalPratices = $row['M'];
                $experimentalDesignId = $row['N'];
                $experimentalDesignDescription = $row['O'];
                $observationUnitDescription = $row['P'];
                // check if the file doesn't have empty columns
                if ($studyName != null && $studyAbbreviation != null && $trialAbbreviation) {
                    // check if the data is upload in the database
                    $existingStudy = $entmanager->getRepository(Study::class)->findOneBy(['abbreviation' => $studyAbbreviation]);
                    // upload data only for objects that haven't been saved in the database
                    if (!$existingStudy) {
                        $study = new Study();
                        if ($this->getUser()) {
                            $study->setCreatedBy($this->getUser());
                        }
                        try {
                            //code...
                            $studyInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instituteId]);
                            if (($studyInstitute != null) && ($studyInstitute instanceof \App\Entity\Institute)) {
                                $study->setInstitute($studyInstitute);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the instcode / institute ID " .$instituteId);
                        }
                        
                        try {
                            //code...
                            $studyTrial = $entmanager->getRepository(Trial::class)->findOneBy(['abbreviation' => $trialAbbreviation]);
                            if (($studyTrial != null) && ($studyTrial instanceof \App\Entity\Trial)) {
                                $study->setTrial($studyTrial);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the trial abbreaviation " .$trialAbbreviation);
                        }

                        try {
                            //code...
                            $studyFactorOntId = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $factorOntologyId]);
                            if (($studyFactorOntId != null) && ($studyFactorOntId instanceof \App\Entity\FactorType)) {
                                $study->setFactor($studyFactorOntId);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the factor ontology Id " .$factorOntologyId);
                        }

                        try {
                            //code...
                            $studyLocation = $entmanager->getRepository(Location::class)->findOneBy(['abbreviation' => $locationAbbreviation]);
                            if (($studyLocation != null) && ($studyLocation instanceof \App\Entity\Location)) {
                                $study->setLocation($studyLocation);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the location " .$locationAbbreviation);
                        }

                        try {
                            //code...
                            $studyGrowthFacilityType = $entmanager->getRepository(GrowthFacilityType::class)->findOneBy(['ontology_id' => $growthFacilityType]);
                            if (($studyGrowthFacilityType != null) && ($studyGrowthFacilityType instanceof \App\Entity\GrowthFacilityType)) {
                                $study->setGrowthFacility($studyGrowthFacilityType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the growth facility type " .$growthFacilityType);
                        }

                        

                        try {
                            //code...
                            if ($growthFacilityDesc) {
                                $study->setGrowthFacilityDescription($growthFacilityDesc);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the growth facility description of the study" .$growthFacilityDesc);
                        }

                        try {
                            //code...
                            $studyExperimentalDesignType = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['ontology_id' => $experimentalDesignId]);
                            if (($studyExperimentalDesignType != null) && ($studyExperimentalDesignType instanceof \App\Entity\ExperimentalDesignType)) {
                                $study->setExperimentalDesignType($studyExperimentalDesignType);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the experimental design type " .$experimentalDesignId);
                        }

                        try {
                            //code...
                            $studySeason = $entmanager->getRepository(Season::class)->findOneBy(['ontology_id' => $season]);
                            if (($studySeason != null) && ($studySeason instanceof \App\Entity\Season)) {
                                $study->setSeason($studySeason);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the season " .$season);
                        }

                        try {
                            //code...
                            $study->setAbbreviation($studyAbbreviation);
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study abbreviation " .$studyAbbreviation);
                        }

                        try {
                            //code...
                            if ($studyName) {
                                $study->setName($studyName);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study name " .$studyName);
                        }

                        try {
                            //code...
                            if ($studyDescription) {
                                $study->setDescription($studyDescription);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the study description " .$studyDescription);
                        }

                        try {
                            //code...
                            if ($startDate) {
                                $study->setStartDate($startDate);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the start date " .$startDate);
                        }

                        try {
                            //code...
                            if ($endDate) {
                                $study->setEndDate($endDate);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the end date " .$endDate);
                        }

                        try {
                            //code...
                            if ($culturalPratices) {
                                $study->setCulturalPractice($culturalPratices);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the cultural practices " .$culturalPratices);
                        }

                        try {
                            //code...
                            if ($experimentalDesignDescription) {
                                $study->setExperimentalDesignDescription($experimentalDesignDescription);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the experimental description " .$experimentalDesignDescription);
                        }

                        try {
                            //code...
                            if ($observationUnitDescription) {
                                $study->setObservationUnitsDescription($observationUnitDescription);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', " there is a problem with the observation unit description " .$observationUnitDescription);
                        }

                        $study->setIsActive(true);
                        $study->setCreatedAt(new \DateTime());
                        try {
                            //code...
                            $entmanager->persist($study);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                    // exceptional for study to update the database without impacting the uppe layers
                    // else {
                    //     if ($this->getUser()) {
                    //         $existingStudy->setCreatedBy($this->getUser());
                    //     }
                    //     try {
                    //         //code...
                    //         $studyInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instituteId]);
                    //         if (($studyInstitute != null) && ($studyInstitute instanceof \App\Entity\Institute)) {
                    //             $existingStudy->setInstitute($studyInstitute);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the instcode / institute ID " .$instituteId);
                    //     }
                        
                    //     try {
                    //         //code...
                    //         $studyTrial = $entmanager->getRepository(Trial::class)->findOneBy(['abbreviation' => $trialAbbreviation]);
                    //         if (($studyTrial != null) && ($studyTrial instanceof \App\Entity\Trial)) {
                    //             $existingStudy->setTrial($studyTrial);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the trial abbreaviation " .$trialAbbreviation);
                    //     }

                    //     try {
                    //         //code...
                    //         $studyFactorOntId = $entmanager->getRepository(FactorType::class)->findOneBy(['ontology_id' => $factorOntologyId]);
                    //         if (($studyFactorOntId != null) && ($studyFactorOntId instanceof \App\Entity\FactorType)) {
                    //             $existingStudy->setFactor($studyFactorOntId);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the factor ontology Id " .$factorOntologyId);
                    //     }

                    //     try {
                    //         //code...
                    //         $studyLocation = $entmanager->getRepository(Location::class)->findOneBy(['abbreviation' => $locationAbbreviation]);
                    //         if (($studyLocation != null) && ($studyLocation instanceof \App\Entity\Location)) {
                    //             $existingStudy->setLocation($studyLocation);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the location " .$locationAbbreviation);
                    //     }

                    //     try {
                    //         //code...
                    //         $studyGrowthFacilityType = $entmanager->getRepository(GrowthFacilityType::class)->findOneBy(['ontology_id' => $growthFacilityType]);
                    //         if (($studyGrowthFacilityType != null) && ($studyGrowthFacilityType instanceof \App\Entity\GrowthFacilityType)) {
                    //             $existingStudy->setGrowthFacility($studyGrowthFacilityType);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the growth facility type " .$growthFacilityType);
                    //     }

                        

                    //     try {
                    //         //code...
                    //         if ($growthFacilityDesc) {
                    //             $existingStudy->setGrowthFacilityDescription($growthFacilityDesc);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the growth facility description of the study" .$growthFacilityDesc);
                    //     }

                    //     try {
                    //         //code...
                    //         $studyExperimentalDesignType = $entmanager->getRepository(ExperimentalDesignType::class)->findOneBy(['ontology_id' => $experimentalDesignId]);
                    //         if (($studyExperimentalDesignType != null) && ($studyExperimentalDesignType instanceof \App\Entity\ExperimentalDesignType)) {
                    //             $existingStudy->setExperimentalDesignType($studyExperimentalDesignType);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the experimental design type " .$experimentalDesignId);
                    //     }

                    //     try {
                    //         //code...
                    //         $studySeason = $entmanager->getRepository(Season::class)->findOneBy(['ontology_id' => $season]);
                    //         if (($studySeason != null) && ($studySeason instanceof \App\Entity\Season)) {
                    //             $existingStudy->setSeason($studySeason);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the season " .$season);
                    //     }

                    //     try {
                    //         //code...
                    //         $existingStudy->setAbbreviation($studyAbbreviation);
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the study abbreviation " .$studyAbbreviation);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($studyName) {
                    //             $existingStudy->setName($studyName);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the study name " .$studyName);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($studyDescription) {
                    //             $existingStudy->setDescription($studyDescription);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the study description " .$studyDescription);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($startDate) {
                    //             $existingStudy->setStartDate($startDate);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the start date " .$startDate);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($endDate) {
                    //             $existingStudy->setEndDate($endDate);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the end date " .$endDate);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($culturalPratices) {
                    //             $existingStudy->setCulturalPractice($culturalPratices);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the cultural practices " .$culturalPratices);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($experimentalDesignDescription) {
                    //             $existingStudy->setExperimentalDesignDescription($experimentalDesignDescription);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the experimental description " .$experimentalDesignDescription);
                    //     }

                    //     try {
                    //         //code...
                    //         if ($observationUnitDescription) {
                    //             $existingStudy->setObservationUnitsDescription($observationUnitDescription);
                    //         }
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', " there is a problem with the observation unit description " .$observationUnitDescription);
                    //     }

                    //     $existingStudy->setIsActive(true);
                    //     $existingStudy->setLastUpdated(new \DateTime());
                    //     try {
                    //         //code...
                    //         $entmanager->persist($existingStudy);
                    //         $entmanager->flush();
                    //     } catch (\Throwable $th) {
                    //         //throw $th;
                    //         $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                    //     }
                    // }
                }
            }
            
            // Query how many rows are there in the table
            $totalStudyAfter = $repoStudy->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalStudyBefore == 0) {
                $this->addFlash('success', $totalStudyAfter . " studies have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalStudyAfter - $totalStudyBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new study has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " study has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " studies have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('study_index'));
        }

        $context = [
            'title' => 'Study Upload From Excel',
            'studyUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('study/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/study_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'study_template_example.xlsx');
        return $response;
       
    }
}

