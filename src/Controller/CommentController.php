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
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}/edit', name: 'app_comment_edit')]
    #[IsGranted('COMMENT_EDIT', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function edit(
        EntityManagerInterface $em,
        Request $request,
        Comment $comment
    ): Response {

        // Extra security (defensive check)
        if ($comment->getAuthor() !== $this->getUser()) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()) {
                $this->addFlash('danger', '❌ Le commentaire est invalide.');
                return $this->redirectToRoute('app_comment_edit', [
                    'id' => $comment->getId()
                ]);
            }

            $comment->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', '✏️ Commentaire modifié avec succès.');

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $comment->getTrick()->getSlug(),
                'id'   => $comment->getTrick()->getId(),
            ]);
        }

        return $this->render('comment/edit.html.twig', [
            'form'    => $form->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('COMMENT_DELETE', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        Comment $comment
    ): Response {

        // CSRF protection
        if (!$this->isCsrfTokenValid('delete_comment_' . $comment->getId(), $request->request->get('_token'))) {

            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $comment->getTrick()->getSlug(),
                'id'   => $comment->getTrick()->getId(),
            ]);
        }

        // Extra security
        if ($comment->getAuthor() !== $this->getUser()) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        $slug = $comment->getTrick()->getSlug();
        $id   = $comment->getTrick()->getId();

        $em->remove($comment);
        $em->flush();

        $this->addFlash('success', '🗑️ Commentaire supprimé avec succès.');

        // Redirect to previous page (best UX)
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