<?php

// src/Controller/VideoController.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 */

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class VideoController extends AbstractController
{
    /**
     * Deletes a video associated with a trick.
     */
    #[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST'])]
    #[IsGranted('VIDEO_DELETE', subject: 'video', message: 'You do not have the required permissions.', statusCode: 403)]
    public function delete(
        Video $video,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        // Get the associated Trick early to ensure null safety
        $trick = $video->getTrick();

        // Safety check: ensure the video is actually linked to a trick
        if (!$trick) {
            $this->addFlash('danger', '❌ This video is not linked to any trick.');
            return $this->redirectToRoute('app_home');
        }

        // Retrieve and cast the CSRF token to string (fixes the "mixed" type error)
        $tokenId = sprintf('delete_video_%d', $video->getId());
        $submittedToken = (string)$request->request->get('_token');

        // SECURITY: Validate CSRF token
        if (!$this->isCsrfTokenValid($tokenId, $submittedToken)) {
            $this->addFlash('danger', '❌ Invalid security token.');

            return $this->redirectToRoute('app_trick_show', [
                'id'   => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        // Perform the deletion
        $em->remove($video);
        $em->flush();

        $this->addFlash('success', '🗑️ Video successfully deleted.');

        // Handling redirection
        $referer = (string)$request->headers->get('referer');

        // Redirect to previous page if it belongs to our domain
        if ($referer !== '' && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        // Fallback: redirect to the trick edit page using our safe $trick variable
        return $this->redirectToRoute('app_trick_edit', [
            'id' => $trick->getId(),
        ]);
    }
}
