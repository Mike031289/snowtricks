<?php

// src/Controller/TrickController.php

/*
 * This file is to handle trick-related requests. It includes actions for creating, editing, deleting, and displaying tricks, as well as adding comments. It also ensures that only authorized users can perform certain actions and provides feedback through flash messages. The controller interacts with the database using Doctrine's EntityManager and handles media uploads through a dedicated MediaService. It also implements pagination for comments and uses Symfony's security features to protect routes and actions.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class TrickController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private MediaService $mediaService,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('profile/tricks', name: 'app_profile_tricks', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'tricks' => $user->getTricks(),
        ]);
    }

    #[Route('/profile/trick/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Safety: Ensure user is an instance of our User entity
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException('You must be logged in.');
            }

            $trick->setAuthor($user);
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trick->setSlug(
                $this->slugger->slug((string) $trick->getName())->lower()
            );

            $this->mediaService->handleMainImage($form, $trick);
            $this->mediaService->handleImages($form, $trick);
            $this->mediaService->handleVideos($form, $trick);

            $this->em->persist($trick);
            $this->em->flush();

            $this->addFlash('success', '✅ Trick créé avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }

    #[Route('/profile/trick/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TRICK_EDIT', subject: 'trick')]
    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new \DateTimeImmutable());
            $trick->setSlug(
                $this->slugger->slug((string) $trick->getName())->lower()
            );

            $this->mediaService->handleMainImage($form, $trick);
            $this->mediaService->handleImages($form, $trick);
            $this->mediaService->handleVideos($form, $trick);

            $this->em->flush();
            $this->addFlash('success', '✏️ Trick modifié avec succès.');

            $referer = (string) $request->headers->get('referer');
            if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'images' => $trick->getImages(),
            'videos' => $trick->getVideos(),
            'mainImage' => $trick->getMainImage(),
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
    }

    #[Route('/profile/trick/{id}/delete', name: 'app_trick_delete', methods: ['POST'])]
    #[IsGranted('TRICK_DELETE', subject: 'trick')]
    public function delete(Request $request, Trick $trick): Response
    {
        $tokenId = sprintf('delete%d', $trick->getId());
        // Explicit cast to string for SymfonyInsight
        $submittedToken = (string) $request->request->get('_token');

        if (!$this->isCsrfTokenValid($tokenId, $submittedToken)) {
            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_profile');
        }

        $this->em->remove($trick);
        $this->em->flush();

        $this->addFlash('success', '🗑️ Trick supprimé avec succès.');

        $referer = (string) $request->headers->get('referer');
        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/{id}/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick, Request $request, CommentRepository $commentRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;

        $comments = $commentRepository->findPaginatedByTrick($trick, $page, $limit);
        $total = $commentRepository->countByTrick($trick);
        $totalPages = (int) ceil($total / $limit);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException('You must be logged in to comment.');
            }

            $comment->setAuthor($user);
            $comment->setTrick($trick);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success', '💬 Commentaire ajouté !');

            $referer = (string) $request->headers->get('referer');
            if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'currentPage' => $page,
            'comments' => $comments,
            'totalPages' => $totalPages,
            'commentForm' => $form->createView(),
        ]);
    }
}
