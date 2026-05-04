<?php

// src/Controller/ImageController.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
 */

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ImageController extends AbstractController
{
    /**
     * Delete an image (file + database)
     */
    #[Route('/image/{id}/delete', name: 'app_image_delete', methods: ['POST'])]
    #[IsGranted('IMAGE_DELETE', subject: 'image', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        Image $image,
    ): Response {

        // CSRF protection
        if (!$this->isCsrfTokenValid('delete_image_'.$image->getId(), $request->request->get('_token'))) {

            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_trick_show', [
                'id'   => $image->getTrick()->getId(),
                'slug' => $image->getTrick()->getSlug(),
            ]);
        }

        // Extra safety (defensive)
        if (!$this->isGranted('IMAGE_DELETE', $image)) {
            $this->addFlash('danger', '❌ Accès refusé.');
            throw $this->createAccessDeniedException();
        }

        $trick = $image->getTrick();

        // Delete physical file
        $filePath = $this->getParameter('images_directory').'/'.$image->getUrl();

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Remove from database
        $em->remove($image);
        $em->flush();

        $this->addFlash('success', '🗑️ Image supprimée avec succès.');

        // Redirect to previous page
        $referer = $request->headers->get('referer');

        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        // Fallback redirect
        return $this->redirectToRoute('app_trick_edit', [
            'id' => $trick->getId(),
        ]);
    }
}
