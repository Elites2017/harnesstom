<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// set a class level route
/**
 * @Route("/admin/user", name="admin_user_")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PersonRepository $personRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $persons = $personRepo->findAll();
        //dd($persons);
        $context = [
            'title' => 'Admin User',
            'persons' => $persons
        ];
        return $this->render('admin_user/index.html.twig', $context);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(User $userSelected): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $context = [
            'title' => 'Admin User Details',
            'user' => $userSelected
        ];
        return $this->render('admin_user/details.html.twig', $context);
    }

    /**
     * @Route("/change_role/{id}", name="change_role")
     */
    public function changeRole(User $user, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($user->getId()) {
            $roles = $user->getRoles();
            $roleSearchBool = false;
            $ind = "";
            // search for the ROLE_ADMIN
            foreach (array_keys($roles, 'ROLE_ADMIN') as $key) {
                $roleSearchBool = true;
                $ind = $key;
            }
            if ($roleSearchBool) {
                unset($roles[$ind]);
                $roles = array_values($roles);
            } else {
                $roles [] = "ROLE_ADMIN";
            }
            $user->setRoles($roles);
        }
        $entmanager->persist($user);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'roleSearchBool' => $roleSearchBool,
            'message' => $roleSearchBool ? ' Make Admin ' : 'Remove Admin'
        ], 200);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function changeStatus(User $user, EntityManagerInterface $entmanager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repositoryPer = $this->getDoctrine()->getRepository(Person::class);
        $onePerson = $repositoryPer->findOneBy(['user' => $user]);
        if ($user->getId()) {
            $user->setIsActive(!$user->getIsActive());
            if ($onePerson) {
                $onePerson->setIsActive(!$onePerson->getIsActive());
            }
        }

        $entmanager->persist($user);
        $entmanager->flush();

        return $this->json([
            'code' => 200,
            'message' => $user->getIsActive()
        ], 200);
        return $this->redirect($this->generateUrl('season_home'));
    }
}
