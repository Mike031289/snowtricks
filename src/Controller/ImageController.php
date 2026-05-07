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
        // First safety check: retrieve and validate the associated Trick
        $trick = $image->getTrick();

        if (!$trick) {
            $this->addFlash('danger', '❌ Cette image n\'est liée à aucun Trick.');
            return $this->redirectToRoute('app_home');
        }

        // We store Trick data now because $image will be removed later
        $trickId = $trick->getId();
        $trickSlug = $trick->getSlug();

        // Retrieve token and cast to string for SymfonyInsight compliance
        $tokenId = sprintf('delete_image_%d', $image->getId());
        $submittedToken = (string)$request->request->get('_token');

        // SECURITY CSRF: check if token is valid
        if (!$this->isCsrfTokenValid($tokenId, $submittedToken)) {
            $this->addFlash('danger', '❌ Action invalide.');

            return $this->redirectToRoute('app_trick_show', [
                'id'   => $trickId,
                'slug' => $trickSlug,
            ]);
        }

        // Physical file deletion
        $imagesDirectory = $this->getParameter('images_directory');
        // Ensure parameter is treated as string
        if (is_string($imagesDirectory)) {
            $filePath = $imagesDirectory . '/' . $image->getUrl();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Database removal
        $em->remove($image);
        $em->flush();

        $this->addFlash('success', '🗑️ Image supprimée avec succès.');

        // Handle redirection logic
        $referer = $request->headers->get('referer');

        if ($referer && str_contains((string)$referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        // Fallback redirect to the edit page of the trick
        return $this->redirectToRoute('app_trick_edit', [
            'id' => $trickId,
        ]);
    }
}
