<?php

namespace App\Controller;

use App\Entity\Inquiry;
use App\Form\InquiryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/inquiry", name="inquiry_")
 */
class InquiryController extends AbstractController
{
    /**
     * @Route("/new", name="new")
     */
    public function create(Request $request, EntityManagerInterface $entmanager, MailerInterface $mailer): Response
    {
        $inquiry = new Inquiry();
        $form = $this->createForm(InquiryType::class, $inquiry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // get the file (name from the InquiryType form)
            $file = $request->files->get('inquiry')['file'];

            $inquiry->setCreatedAt(new \DateTime());
            $entmanager->persist($inquiry);
            $entmanager->flush();

            // email for the user
            $email = (new TemplatedEmail())
                ->from(new Address('noreply-inquiry@harnesstom.eu', 'HarnessTom Inquiry'))
                ->to($inquiry->getEmail())
                ->subject('Inquiry Confirmation')
                // path to your Twig template
                ->htmlTemplate('inquiry/reception.html.twig')
            ;
            $mailer->send($email);

            // email for harnesstom team
            $email_teamHDB = (new TemplatedEmail())
                ->from(new Address('noreply-inquiry@harnesstom.eu', 'HarnessTom Inquiry'))
                ->subject($inquiry->getSubject())
                ->html(
                    "<h4>" .$inquiry->getEmail(). " wrote </h4>
                    <h3>" .$inquiry->getMessage(). "</h3>");

                if ($file) {
                    $email_teamHDB->attach(fopen($file->getPathname(), 'r'), $file->getClientOriginalName());
                }

                if ($inquiry->getType() == 'Data Curation') {
                    $email_teamHDB->to('cpons@upvnet.upv.es')
                        ->cc('david.pierre@toulouse-inp.fr');
                } else {
                    $email_teamHDB->to('david.pierre@toulouse-inp.fr')
                        ->cc('cpons@upvnet.upv.es');

                }
            ;
            $mailer->send($email_teamHDB);

            $this->addFlash('success', 'Thank you for your inquiry, an email has been sent to you.');
            return $this->redirect($this->generateUrl('app_home'));
        }

        $context = [
            'title' => 'New Inquiry',
            'inquiryForm' => $form->createView()
        ];
        return $this->render('inquiry/new.html.twig', $context);
    }
}
