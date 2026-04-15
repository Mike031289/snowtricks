<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trick')]
final class TrickController extends AbstractController
{
    // List all tricks
    #[Route('/liste', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    // Create a new trick
    #[Route('/ajouter', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger,
        ): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, [
            'is_edit' => false, // pass an option to indicate we are in create mode
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Author (associate current user as author of the trick)
            $trick->setAuthor($this->getUser());
            
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $slug = $slugger->slug($trick->getName())->lower();
            $trick->setSlug($slug);
            
            // main image (single file input)
            $mainImageFile = $form->get('mainImage')->getData();

            if ($mainImageFile) {
                $newFilename = uniqid().'.'.$mainImageFile->guessExtension();

                $mainImageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                
                $trick->setMainImage($newFilename);
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Show a single trick by slug and handle comment form submission on the same page (POST request) and display the trick details (GET request)
    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'],)]
    public function show(Trick $trick, Request $request, EntityManagerInterface $em): Response
    {
        
        /** @var Comment $comment The comment entity that will be used to create a new comment */
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment); 
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setTrick($trick);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }
        
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $commentForm->createView()
        ]);
    }

    // Edit an existing trick
    #[Route('/{slug}/modifier', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trick $trick,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
        ): Response 
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'is_edit' => true, // pass an option to indicate we are in edit mode
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUpdatedAt(new \DateTimeImmutable());

            // regenerate slug if name changed
            $slug = $slugger->slug($trick->getName())->lower();
            $trick->setSlug($slug);

            // handle main image
            $mainImageFile = $form->get('mainImage')->getData();

            if ($mainImageFile) {

                $newFilename = uniqid($trick->getSlug().'_').'.'.$mainImageFile->guessExtension();

                $mainImageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $trick->setMainImage($newFilename);
                
            }

            // handle additional images (multiple file input)
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
                    $image->setTrick($trick); // associate the image with the trick
                    $trick->addImage($image);
                    
                    $entityManager->persist($image);
                }
            }
            
            // handle videos (YouTube links)
            $videosString = $form->get('videos')->getData();

            if ($videosString) {

                $videos = array_map('trim', explode(',', $videosString));

                foreach ($videos as $videoUrl) {

                    if ($videoUrl) {

                        $embedUrl = $this->convertToEmbedUrl($videoUrl);

                        if ($embedUrl) {
                            $video = new Video();
                            $video->setEmbedUrl($embedUrl); // embed direct
                            $video->setIsMain(false);
                            $video->setCreatedAt(new \DateTimeImmutable());
                            $video->setTrick($trick);

                            $entityManager->persist($video);
                        }
                    }
                }
            }

            $entityManager->flush();
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    // Delete a trick
    #[Route('/{slug}/supprimer', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getSlug(), $request->request->get('_token'))) {
            
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');
    }

    private function convertToEmbedUrl(string $url): ?string
    {
        $url = trim($url);

        // youtube.com/watch?v=XXXX
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // youtu.be/XXXX
        if (preg_match('/youtu\.be\/([^?&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // embed OK
        if (preg_match('/youtube\.com\/embed\/([^?&]+)/', $url, $matches)) {
            return $url;
        }

        return null;
    }

}