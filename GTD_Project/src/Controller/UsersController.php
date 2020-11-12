<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Users;
use App\Form\RegisterType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UsersController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     */

    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $user = new Users();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $passwordEncrypt = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncrypt);
            $user->setRoles("ROLE_USER");
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("login");
        }

        return $this->render('users/register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->render('users/login.html.twig', [
            "lastUserName" => $utils->getLastUsername(),
            "error" => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirectToRoute("register");
    }

    /**
     * @Route("/profile", name="profile", methods={"GET", "POST"})
     * @param Security $security
     * @param UsersRepository $usersRepository
     * @return Response
     */
    public function showProfile(Security $security, UsersRepository $usersRepository)
    {
        return $this->render('users/profile.html.twig', [
            "user" => $security->getUser()
        ]);
    }

    /**
     * @Route("/profile/{id}", name="edit_profile", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param Security $security
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, Security $security)
    {
        $utilisateur = $security->getUser();
        $form = $this->createForm(RegisterType::class, $utilisateur);
        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid())
        {
            $passwordEncrypt = $encoder->encodePassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($passwordEncrypt);
            $utilisateur->setRoles("ROLE_USER");
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            return $this->redirectToRoute("profile");
        }

        return $this->render('users/edit_profile.html.twig', [
            "form" => $form->createView(),
            "utilisateur" => $utilisateur

        ]);
    }

    /**
     * @Route("/profile", name="delete_profile", methods={"DELETE"})
     * @param Request $request
     * @param Session $session
     * @param Security $security
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProfile(Request $request, Session $session, Security $security)
    {

        if ($this->isCsrfTokenValid('delete'.$security->getUser()->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $security->getUser()->getId())
            {
                $session = $this->get('session');
                $session = new Session();
                $session->invalidate();
            }
            $entityManager->remove($security->getUser());
            $entityManager->flush();
        }

        return $this->redirectToRoute("register");
    }
}
