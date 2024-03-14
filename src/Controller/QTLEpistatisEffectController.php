<?php

namespace App\Controller;

use App\Entity\QTLEpistasisEffect;
use App\Form\QTLEpistasisEffectType;
use App\Repository\QTLEpistasisEffectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/qtl/epistasis/effect", name="qtl_epistasis_effect_")
 */
class QTLEpistatisEffectController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(QTLEpistasisEffectRepository $qtlEpistasisRepo): Response
    {
        $qtlEpistasisEffects = $qtlEpistasisRepo->findAll();
        $context = [
            'title' => 'QTL Epistasis Effect List',
            'qtlEpistasisEffects' => $qtlEpistasisEffects
        ];
        return $this->render('qtl_epistasis_effect/index.html.twig', $context);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $qtlEpistasisEffect = new QTLEpistasisEffect();
        $form = $this->createForm(QTLEpistasisEffectType::class, $qtlEpistasisEffect);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $qtlEpistasisEffect->setCreatedBy($this->getUser());
            }
            $qtlEpistasisEffect->setIsActive(true);
            $qtlEpistasisEffect->setCreatedAt(new \DateTime());
            $entmanager->persist($qtlEpistasisEffect);
            $entmanager->flush();
            $this->addFlash('success', " one element has been successfuly added");
            return $this->redirect($this->generateUrl('qtl_epistasis_effect_index'));
        }

        $context = [
            'title' => 'QTL Epistasis Creation',
            'qtlEpistasisEffectForm' => $form->createView()
        ];
        return $this->render('qtl_epistasis_effect/create.html.twig', $context);
    }
}
