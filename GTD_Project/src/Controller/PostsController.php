<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Form\CommentsType;
use App\Form\PostsType;
use App\Repository\CommentsRepository;
use App\Repository\FriendshipRepository;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Symfony\Component\Security\Core\Security;
use App\Repository\UsersRepository;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts", name="posts_index", methods={"GET"})
     */
    public function index(PostsRepository $postsRepository): Response
    {
        return $this->render('posts/index.html.twig', [
            'posts' => $postsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/posts/new", name="posts_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security, UsersRepository $usersRepository): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUser($security->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/{id}", name="posts_show")
     */
    public function show(Posts $post, CommentsRepository $commentsRepository, Request $request, Security $security, EntityManagerInterface $entityManager, FriendshipRepository $friendshipRepository): Response
    {


        $authorization = $friendshipRepository->checkRelationFriendsExist($security->getUser()->getId(), $post->getUser()->getId());

        $newComments = new Comments();

        $form = $this->createForm(CommentsType::class, $newComments);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $newComments->setUser($security->getUser())
                ->setPost($post)
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone("Europe/Paris")));
            $entityManager->persist($newComments);
            $entityManager->flush();

            return $this->redirectToRoute("posts_show", [
                "id" => $post->getId()
            ]);

        }

        $comments = $commentsRepository->findCommentsOfAPost($post->getId());

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            "form" => $form->createView(),
            "authorization" => $authorization
        ]);
    }

    /**
     * @Route("/posts/{id}/edit", name="posts_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Posts $post): Response
    {
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/{id}", name="posts_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Posts $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('posts_index');
    }

    /**
     * @Route("/", name="all_posts")
     * @param FriendshipRepository $friendshipRepository
     * @param Security $security
     * @param PostsRepository $postsRepository
     * @return Response
     */
    public function getAllPosts(FriendshipRepository $friendshipRepository, Security $security, PostsRepository $postsRepository)
    {
        $friends = $friendshipRepository->getAllRelationFriendsOfUser($security->getUser()->getId());

        $arrayFriendsId = [];

        for($i = 0; $i < count($friends); $i++)
        {
            if($friends[$i]->getSender()->getId() === $security->getUser()->getId())
            {
                array_push($arrayFriendsId, $friends[$i]->getTarget()->getId());
            }
            else{
                array_push($arrayFriendsId, $friends[$i]->getSender()->getId());
            }
        }

        $posts = [];

        for($i = 0; $i < count($arrayFriendsId); $i++)
        {
            if($postsRepository->getAllPostsOfMyFriends($arrayFriendsId[$i]) !== [])
            {
                array_push($posts, $postsRepository->getAllPostsOfMyFriends($arrayFriendsId[$i]));
            }

        }

        return $this->render('posts/fil_actualite.html.twig', [
            "posts" => $posts
        ]);
    }

    /**
     * @Route("/posts/comment/{id}", name="edit_comment", methods={"GET", "POST"})
     * @param Request $request
     * @param Comments $comments
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editComment(Request $request, Comments $comments)
    {
        $form = $this->createForm(CommentsType::class, $comments);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comments->setUpdatedAt(new \DateTime('now', new \DateTimeZone("Europe/Paris")));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("posts_show", [
                "id" => $comments->getPost()->getId()
            ]);
        }

        return $this->render('posts/edit_comment.html.twig', [
            'comment' => $comments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/posts/comment/{id}", name="delete_comment", methods={"DELETE"})
     */
    public function deleteComment(Comments $comments, Request $request)
    {

        if ($this->isCsrfTokenValid('delete'.$comments->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comments);
            $entityManager->flush();
        }

        return $this->redirectToRoute("posts_show", [
            "id" => $comments->getPost()->getId()
        ]);
    }

}
