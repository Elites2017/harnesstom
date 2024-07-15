<?php

namespace App\Controller;

use App\Entity\DataSubmission;
use App\Form\DataSubmissionType;
use App\Repository\DataSubmissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/data/submission", name="data_submission_")
 */
class DataSubmissionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $context = [
            'title' => 'Data Submission Guide',
        ];
        return $this->render('data_submission/index.html.twig', $context);
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(DataSubmissionRepository $dataSubRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $dataSubmissions =  $dataSubRepo->findAll();
        $context = [
            'title' => 'Data Submission Request',
            'dataSubmissions' => $dataSubmissions
        ];
        return $this->render('data_submission/list.html.twig', $context);
    }

    /**
     * @Route("/new", name="new")
     */
    public function create(Request $request, EntityManagerInterface $entmanager, MailerInterface $mailer): Response
    {
        $dataSubmission = new DataSubmission();
        $form = $this->createForm(DataSubmissionType::class, $dataSubmission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // get the file (name from the CountryUploadFromExcelType form)
            $file = $request->files->get('data_submission')['file'];
            // set the folder to send the file to
            $fileFolder = __DIR__ . '/../../public/uploads/datasubmission/';
            // apply md5 function to generate a unique id for the file and concat it with the original file name
            if ($file->getClientOriginalName()) {
                $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                    $dataSubmission->setFile($filePathName);
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->addFlash('danger', "Fail to upload the file, try again ");
                }
            } else {
                $this->addFlash('danger', "Error in the file name, try to rename the file and try again");
            }

            $dataSubmission->setCreatedAt(new \DateTime());
            $entmanager->persist($dataSubmission);
            $entmanager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('noreply-datareception@harnesstom.eu', 'HarnessTom Data Reception'))
                ->to($dataSubmission->getEmail())
                ->subject('Data Reception Confirmation')
                ->cc('david.pierre@toulouse-inp.fr', 'cpons@upvnet.upv.es')
                // path to your Twig template
                ->htmlTemplate('data_submission/data_reception.html.twig')
            ;
            $mailer->send($email);
            $this->addFlash('success', 'Thank you for your data submission request, an email has been sent to you.');
            return $this->redirect($this->generateUrl('data_submission_index'));
        }

        $context = [
            'title' => 'New Data Submission',
            'dataSubmissionForm' => $form->createView()
        ];
        return $this->render('data_submission/new.html.twig', $context);
    }

    /**
     * @Route("/before_starting", name="before_starting")
     */
    public function beforeStarting(): Response
    {
        $context = [
            'title' => 'Before you start'
        ];
        return $this->render('data_submission/before_starting.html.twig', $context);
    }

    /**
     * @Route("/submit", name="submit")
     */
    public function dataSubmission(): Response
    {
        $context = [
            'title' => 'Data curation'
        ];
        return $this->render('data_submission/data_submission.html.twig', $context);
    }

    /**
     * @Route("/after_submission", name="after_submission")
     */
    public function afterSubmission(): Response
    {
        $context = [
            'title' => 'After the submission'
        ];
        return $this->render('data_submission/after_submission.html.twig', $context);
    }

    /**
     * @Route("/download_template", name="download_template")
     */
    public function downloadTemplate(): Response
    {
        $response = new BinaryFileResponse('../public/todownload/harnesstom_database_templates20230405.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'harnesstom_database_templates20230405.xlsx');
        return $response;
       
    }
}
