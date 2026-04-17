<?php

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
    // This controller is only responsible for deleting images, as they are uploaded and managed in the TrickController.
    #[Route('/image/{id}/delete', name: 'app_image_delete', methods: ['POST'])]
    #[IsGranted('IMAG_DELETE', subject: 'image', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(Image $image, Request $request, EntityManagerInterface $em): Response
    {

        if ($this->isCsrfTokenValid('delete_image_'.$image->getId(), $request->request->get('_token'))) {

            $trick = $image->getTrick();

            // remove file (optional but propre)
            $filePath = $this->getParameter('images_directory').'/'.$image->getUrl();
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $em->remove($image);
            $em->flush();

            return $this->redirectToRoute('app_trick_edit', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->redirectToRoute('app_home');
    }
    
}