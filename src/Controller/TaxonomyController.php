<?php

namespace App\Controller;

use App\Entity\Taxonomy;
use App\Form\TaxonomyType;
use App\Form\TaxonomyUpdateType;
use App\Repository\TaxonomyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
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
}
