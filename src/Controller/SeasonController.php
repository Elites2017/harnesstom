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
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
}
