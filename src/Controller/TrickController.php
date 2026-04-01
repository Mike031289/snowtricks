<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\GroupRepository;
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
        GroupRepository $groupRepository
        ): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Author (associate current user as author of the trick)
            $trick->setAuthor($this->getUser());
            $defaultGroup = $groupRepository->find(1);
            $trick->setGroups($defaultGroup); // assign to default group (id=1) for now. You can change this logic to allow user to select group in the form.
            
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

            // additional images (collection of file inputs)
            $images = $form->get('images')->getData();

            foreach ($images as $imageFile) {
                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();

                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // create a new Image entity and associate it with the Trick
                    $image = new Image();
                    $image->setUrl($newFilename);
                    $image->setTrick($trick);

                    $entityManager->persist($image);
                }
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    // Show a single trick by slug
    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    // Edit an existing trick
    #[Route('/{slug}/modifier', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    // Delete a trick
    #[Route('/{slug}/supprimer', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getSlug(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}