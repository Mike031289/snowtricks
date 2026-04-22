<?php
// src/Controller/VideoController.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
 */

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class VideoController extends AbstractController
{
    #[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST'])]
    #[IsGranted('VIDEO_DELETE', subject: 'video', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(
        Video $video,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        // CSRF check
        if (!$this->isCsrfTokenValid('delete_video_' . $video->getId(), $request->request->get('_token'))) {

            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_trick_show', [
                'id'   => $video->getTrick()->getId(),
                'slug' => $video->getTrick()->getSlug(),
            ]);
        }

        // Extra safety (defensive check)
        if (!$this->isGranted('VIDEO_DELETE', $video)) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        $em->remove($video);
        $em->flush();

        $this->addFlash('success', '🗑️ Vidéo supprimée avec succès.');

        // Redirect back to edit page of trick
        $referer = $request->headers->get('referer');

        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        // Fallback redirect
        return $this->redirectToRoute('app_trick_edit', [
            'id' => $video->getTrick()->getId(),
        ]);
        
    }
}