<?php

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
    #[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST'])]
    #[IsGranted('VIDEO_DELETE', subject: 'video', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(Video $video, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_video_'.$video->getId(), $request->request->get('_token'))) {
            
                $trick = $video->getTrick();
                
                // remove file (optional but propre)
                $filePath = $this->getParameter('videos_directory').'/'.$video->getEmbedUrl();
            
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $em->remove($video); 
                $em->flush();

                return $this->redirectToRoute('app_trick_edit', [
                    'slug' => $trick->getSlug()
                ]);
        }

        return $this->redirectToRoute('app_home');
        
    }
}