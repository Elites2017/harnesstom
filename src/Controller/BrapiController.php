<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
 /**
 * @Route("brapi", name="brapi_")
 */
class BrapiController extends AbstractController
{
    /**
     * @Route("/graphical/filtering", name="graphical_filtering")
     */
    public function graphicalFiltering(): Response
    {
        $context = [
            'title' => 'Graphical Filtering',
        ];
        return $this->render('brapi/graphical_filtering.html.twig', $context);
    }

    /**
     * @Route("/studycomp", name="studycomp")
     */
    public function studyComp(): Response
    {
        $context = [
            'title' => 'Study Comp',
        ];
        return $this->render('brapi/study_comparison.html.twig', $context);
    }

}