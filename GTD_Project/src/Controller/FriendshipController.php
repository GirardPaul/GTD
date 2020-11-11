<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\Users;
use App\Repository\FriendshipRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FriendshipController extends AbstractController
{
    // List of user
    /**
     * @param UsersRepository $usersRepository
     * @Route("/list", name="list_users")
     */
    public function listUser(UsersRepository $usersRepository)
    {
        $list = $usersRepository->findAll();

        return $this->render('friendship/list.html.twig', [
            "users" => $list
        ]);
    }

    // Ask for friend

    /**
     * @Route("/add/{id}", name="add_friend")
     * @param $id
     * @param Security $security
     * @param FriendshipRepository $friendshipRepository
     * @param EntityManagerInterface $entityManager
     * @param UsersRepository $usersRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function friendRequest($id, Security $security, FriendshipRepository $friendshipRepository, EntityManagerInterface $entityManager, UsersRepository $usersRepository)
    {

        if($friendshipRepository->checkRelationExist($security->getUser()->getId(), $id) !== null)
        {
            $friendShip = new Friendship();
            $friendShip->setSender($security->getUser())
                ->setTarget($usersRepository->find($id));
            $entityManager->persist($friendShip);
            $entityManager->flush();

            return $this->redirectToRoute("list_users");
        }
    }

    // List ask for friend of user

    /**
     * @Route("/ask", name="ask_friend")
     * @param FriendshipRepository $friendshipRepository
     * @param Security $security
     * @return Response
     */
    public function friendRequestOfAUser(FriendshipRepository $friendshipRepository, Security $security)
    {

        $ask = $friendshipRepository->checkAskForFriend($security->getUser()->getId());

        return $this->render('friendship/ask.html.twig', [
            "asks" => $ask
        ]);
    }


    // Delete a ask for friend

    /**
     * @Route("/ask/delete/{id}", name="ask_delete")
     * @param $id
     * @param FriendshipRepository $friendshipRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteAsk($id, FriendshipRepository $friendshipRepository, EntityManagerInterface $entityManager): Response
    {

        $relation = $friendshipRepository->find($id);
        $entityManager->remove($relation);
        $entityManager->flush();

        return $this->redirectToRoute('list_users');
    }


    // Accept a ask for friend

    /**
     * @Route("/ask/accept/{id}", name="ask_accept")
     */
    public function accept($id, FriendshipRepository $friendshipRepository, EntityManagerInterface $entityManager): Response
    {
            $date = new \DateTime();
            $relation = $friendshipRepository->find($id);
            $relation->setAcceptedAt($date);
            $entityManager->persist($relation);
            $entityManager->flush();

        return $this->redirectToRoute('list_users');
    }



    // List friends of user

    /**
     * @Route("/friends", name="friends")
     */
    public function friendsOfAUser(FriendshipRepository $friendshipRepository, Security $security)
    {
        $friends = $friendshipRepository->getAllRelationFriendsOfUser($security->getUser()->getId());

        return $this->render('friendship/friends.html.twig', [
            "friends" => $friends
        ]);
    }
}
