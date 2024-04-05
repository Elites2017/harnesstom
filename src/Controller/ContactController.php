<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Institute;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\ContactUpdateType;
use App\Form\UploadFromExcelType;
use App\Repository\ContactRepository;
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
 * @Route("/contact", name="contact_")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ContactRepository $contactRepo): Response
    {
        $contacts =  $contactRepo->findAll();
        $context = [
            'title' => 'Contact List',
            'contacts' => $contacts
        ];
        return $this->render('contact/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $contact->setCreatedBy($this->getUser());
            }
            $contact->setIsActive(true);
            $contact->setCreatedAt(new \DateTime());
            $entmanager->persist($contact);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('contact_index'));
        }

        $context = [
            'title' => 'Contact Creation',
            'contactForm' => $form->createView()
        ];
        return $this->render('contact/create.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(Contact $contactSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Contact Details',
            'contact' => $contactSelected
        ];
        return $this->render('contact/details.html.twig', $context);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Contact $contact, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('contact_edit', $contact);
        $form = $this->createForm(ContactUpdateType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($contact->getInstitute() != null) {
                $entmanager->persist($contact);
                $entmanager->flush();
                $this->addFlash('success', " one element has been successfuly updated");
                return $this->redirect($this->generateUrl('contact_index'));
            } else {
                $this->addFlash('danger', "Error in the form, you must select an institute");
            }
        }

        $context = [
            'title' => 'Contact Update',
            'contactForm' => $form->createView()
        ];
        return $this->render('contact/edit.html.twig', $context);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Contact $contact, Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($contact->getId()) {
            $contact->setIsActive(!$contact->getIsActive());
        }
        $entmanager->persist($contact);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $contact->getIsActive()
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
            $repoContact = $entmanager->getRepository(Contact::class);
            // Query how many rows are there in the Contact table
            $totalContactBefore = $repoContact->createQueryBuilder('tab')
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
                $orcid = $row['A'];
                $email = $row['B'];
                $instcode = $row['C'];
                $type = $row['D'];
                // check if the file doesn't have empty columns
                if ($orcid != null & $email != null) {
                    // check if there is any existed user with this email
                    $existingContact = $entmanager->getRepository(Contact::class)->findOneBy(['orcid' => $orcid]);
                    // if the contact doesn't exist
                    if (!$existingContact) {
                        // create a new object
                        $contact =  new Contact();
                        // go get the contact (from user) of the contact
                        $existingUser = $entmanager->getRepository(User::class)->findOneBy(['email' => $email]);
                        if ($existingUser) {
                            $contact->setPerson($existingUser->getPerson());
                        }
                        // go get the institute of the contact
                        $contactInstitute = $entmanager->getRepository(Institute::class)->findOneBy(['instcode' => $instcode]);
                        if ($contactInstitute) {
                            $contact->setInstitute($contactInstitute);
                        }
                        
                        $contact->setOrcid($orcid);
                        $contact->setType($type);
                        if ($this->getUser()) {
                            $contact->setCreatedBy($this->getUser());
                        }
                        $contact->setIsActive(true);
                        $contact->setCreatedAt(new \DateTime());
                        
                        try {
                            //code...
                            $entmanager->persist($contact);
                            $entmanager->flush();
                        } catch (\Throwable $th) {
                            //throw $th;
                            $this->addFlash('danger', "A problem happened, we can not save your data now due to: " .strtoupper($th->getMessage()));
                        }
                    }
                }
            }
            // Query how many rows are there in the Country table
            $totalContactAfter = $repoContact->createQueryBuilder('tab')
                // Filter by some parameter if you want
                // ->where('a.isActive = 1')
                ->select('count(tab.id)')
                ->getQuery()
                ->getSingleScalarResult();

            if ($totalContactBefore == 0) {
                $this->addFlash('success', $totalContactAfter . " contacts have been successfuly added");
            } else {
                $diffBeforeAndAfter = $totalContactAfter - $totalContactBefore;
                if ($diffBeforeAndAfter == 0) {
                    $this->addFlash('success', "No new Contact has been added");
                } else if ($diffBeforeAndAfter == 1) {
                    $this->addFlash('success', $diffBeforeAndAfter . " Contact has been successfuly added");
                } else {
                    $this->addFlash('success', $diffBeforeAndAfter . " contacts have been successfuly added");
                }
            }
            return $this->redirect($this->generateUrl('contact_index'));
        }

        $context = [
            'title' => 'Contact Upload From Excel',
            'contactUploadFromExcelForm' => $form->createView()
        ];
        return $this->render('contact/upload_from_excel.html.twig', $context);
    }

    /**
     * @Route("/download-template", name="download_template")
     */
    public function excelTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/contact_template_example.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'contact_template_example.xlsx');
        return $response;
       
    }
}
