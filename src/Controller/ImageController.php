<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ImageController extends AbstractController
{
    // #[Route('/image', name: 'app_image')]
    // public function index(): Response
    // {
    //     return $this->render('image/index.html.twig', [
    //         'controller_name' => 'ImageController',
    //     ]);
    // }
    
    #[Route('/ajouter-images', name: 'app_images_add')]
    public function addImages(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageType::class, null, [
            'trick' => $trick, // pass the trick to the form to associate images with it
        ]);
        
        $form->handleRequest($request);
        
        $images = $form->get('images')->getData();

        foreach ($images as $imageFile) {

            if ($imageFile) {

                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $image = new Image();
                $image->setUrl($newFilename);
                $image->setAlt($trick->getName().' image');
                $image->setIsMain(false);
                $image->setCreatedAt(new \DateTimeImmutable());
                $image->setTrick($trick);

                $entityManager->persist($image);
            }
        }
        
        $entityManager->flush();
        return $this->redirectToRoute('app_trick_edit', ['slug' => $trick->getSlug()]);
    }
    
}