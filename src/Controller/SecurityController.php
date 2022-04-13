<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // redirect the logged in user to the home page 
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/change_password", name="change_password")
     */
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entmanager)
    {
        // DP on April 12th 2022
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('oldPassword')->getData();
            $user = $this->getUser();
            $checkPassword = $passwordEncoder->isPasswordValid($user, $old_pwd);
            if ($checkPassword) {
                $new_pwd = $form->get('plainPassword')->getData();
                $user->setPassword($passwordEncoder->hashPassword($user, $new_pwd));
                $entmanager->persist($user);
                $entmanager->flush();
                $this->addFlash('success', "Your password has been successfuly updated");
                return $this->redirect($this->generateUrl('app_home'));
            } else {
                $this->addFlash('old_password_error', "The password you typed doesn't match your old password");
            }
        }
        $context = [
            'title' => 'Change Password',
            'changePasswordForm' => $form->createView()
        ];
        return $this->render('change_password/change_password.html.twig', $context);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
