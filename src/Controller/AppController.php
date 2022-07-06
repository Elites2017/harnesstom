<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll();
        if (count($users) === 0) {
            $user = new User();
            $user->setEmail("harnesstom@harnesstom.eu");
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        "123456"
                    )
                );
                $user->setIsActive(true);
                $user->setIsVerified(true);
                $roles [] = "ROLE_ADMIN";
                $user->setRoles($roles);
                $entityManager->persist($user);
                $entityManager->flush();
        }
        return $this->render('app/index.html.twig', [
            'title' => 'Home Page',
        ]);
    }

    // /**
    //  * @Route("/", name="index")
    //  */
    // public function index(AttributeRepository $attributeRepo): Response
    // {
    //     $attributes =  $attributeRepo->findAll();
    //     $context = [
    //         'title' => 'Attribute List',
    //         'attributes' => $attributes
    //     ];
    //     return $this->render('attribute/index.html.twig', $context);
    // }
}
