<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Person;
use App\Entity\User;
use App\Form\PersonType;
use App\Form\PersonUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/person", name="person_")
 */
class PersonController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PersonRepository $personRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $people =  $personRepo->findAll();
        $context = [
            'title' => 'Person',
            'people' => $people
        ];
        return $this->render('person/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $person = new person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $person->setCreatedBy($this->getUser());
            }
            $person->setIsActive(true);
            $person->setCreatedAt(new \DateTime());
            $entmanager->persist($person);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('person_index'));
        }

        $context = [
            'title' => 'Person Creation',
            'personForm' => $form->createView()
        ];
        return $this->render('person/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Person $peopleelected): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $context = [
            'title' => 'Person Details',
            'person' => $peopleelected
        ];
        return $this->render('person/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Person $person, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(PersonUpdateType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entmanager->persist($person);
            $entmanager->flush();
            return $this->redirect($this->generateUrl('person_index'));
        }

        $context = [
            'title' => 'Person Update',
            'personForm' => $form->createView()
        ];
        return $this->render('person/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Person $person, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($person->getId()) {
            $person->setIsActive(!$person->getIsActive());
            if ($person->getUser()) {
                $user = $person->getUser();
                $user->setIsActive(!$user->getIsActive());
            }
        }
        $entmanager->persist($person);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $person->getIsActive()
        ], 200);
    }

    // this is to upload data in bulk using an excel file
    /**
     * @Route("/upload-from-excel", name="upload_from_excel")
     */
    public function uploadFromExcel(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(UploadFromExcelType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Setup repository of some entity
            $repoPerson = $entmanager->getRepository(Person::class);
            // Query how many rows are there in the Person table
            $totalPersonBefore = $repoPerson->createQueryBuilder('tab')
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
                $email = $row['A'];
                $firstName = $row['B'];
                $middleName = $row['C'];
                $lastName = $row['D'];
                $phoneNumber = $row['E'];
                $streetNumber = $row['F'];
                $postalCode = $row['G'];
                $city = $row['H'];
                $countryISO3 = $row['I'];
                // check if the file doesn't have empty columns
                if ($email != null & $firstName != null) {
                    // check if there is any existed user with this email
                    $existingUser = $entmanager->getRepository(User::class)->findOneBy(['email' => $email]);
                    // if the user doesn't exist, so is the person
                    if (!$existingUser) {
                        // create a new user for him / her
                        $user =  new User();
                        $user->setEmail($email);
                        // encode the plain password
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                    $user,
                                    "pass1234"
                                )
                            );
                        $user->setIsActive(true);
                        $entmanager->persist($user);

                        $person = new Person();
                        $personCountry = $entmanager->getRepository(Country::class)->findOneBy(['iso3' => $countryISO3]);
                        if (($personCountry != null) && ($personCountry instanceof \App\Entity\Country)) {
                            $person->setCountry($personCountry);
                        }
                        if ($this->getUser()) {
                            $person->setCreatedBy($this->getUser());
                        }
                        $person->setUser($user);
                        $person->setfirstName($firstName);
                        $person->setMiddleName($middleName);
                        $person->setLastName($lastName);
                        $person->setStreetNumber($streetNumber);
                        $person->setPostalCode($postalCode);
                        $person->setPhoneNumber($phoneNumber);
                        $person->setCity($city);
                        $person->setIsActive(true);
                        $person->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($person);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // Query how many rows are there in the Country table
            $totalPersonAfter = $repoPerson->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalPersonBefore == 0) {
                $this->addFlash('success', $totalPersonAfter . " People have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalPersonAfter - $totalPersonBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Person has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Person has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " People have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('person_index'));
        }

        $context = [
            'title' => 'Person Upload From Excel',
            'personUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('person/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/person_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'person_template_example.xlsx');
        return $response;
       
    }
}

