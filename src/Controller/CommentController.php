<?php

// src/Controller/CommentController.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    #[IsGranted('COMMENT_EDIT', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function edit(
        EntityManagerInterface $em,
        Request $request,
        Comment $comment,
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();

        // Extra security (defensive check) to ensure author consistency
        if ($comment->getAuthor() !== $user) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', '✏️ Commentaire modifié avec succès.');

            // Retrieve associated trick and check for null to prevent Insight errors
            $trick = $comment->getTrick();
            if (!$trick) {
                return $this->redirectToRoute('app_home');
            }

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $trick->getSlug(),
                'id'   => $trick->getId(),
            ]);
        }

        return $this->render('comment/edit.html.twig', [
            'commentForm' => $form->createView(),
            'comment'     => $comment,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('COMMENT_DELETE', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        Comment $comment,
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();

        $trick = $comment->getTrick();
        // Fallback if trick is missing
        if (!$trick) {
            $this->addFlash('danger', '❌ Le trick associé est introuvable.');
            return $this->redirectToRoute('app_home');
        }

        // Retrieve id and token submitted.
        $tokenId = sprintf('delete_comment_%d', $comment->getId());
        // Cast to string to ensure type safety for isCsrfTokenValid
        $submittedToken = (string)$request->request->get('_token');

        // SECURITY CSRF check
        if (!$this->isCsrfTokenValid($tokenId, $submittedToken)) {
            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $trick->getSlug(),
                'id'   => $trick->getId(),
            ]);
        }

        // Extra security check for author
        if ($comment->getAuthor() !== $user) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        // Store data before deletion for the final redirect
        $slug = $trick->getSlug();
        $id   = $trick->getId();

        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', '🗑️ Commentaire supprimé avec succès.');

        // Redirect to previous page if it belongs to the same host
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        // Fallback redirect
        return $this->redirectToRoute('app_trick_show', [
            'slug' => $slug,
            'id'   => $id,
        ]);
    }
}
