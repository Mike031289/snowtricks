<?php
// src/Controller/TrickController.php
/**
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\MediaService;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TrickController extends AbstractController
{
    public function __construct(
        private MediaService $mediaService
    ) {
    }

    #[Route('profile/tricks', name: 'app_profile_tricks', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $tricks = $user->getTricks();
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'tricks' => $tricks,

        ]);
    }

    #[Route('/profile/trick/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
    
        $trick = new Trick();
        
        $form = $this->createForm(TrickType::class, $trick, [
            'is_edit' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $trick->setAuthor($this->getUser());
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trick->setSlug(
                $slugger->slug($trick->getName())->lower()
            );

            // MAIN IMAGE + MEDIA
            $this->mediaService->handleMainImage($form, $trick);
            $this->mediaService->handleImages($form, $trick);
            $this->mediaService->handleVideos($form, $trick);

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/profile/trick/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TRICK_EDIT', subject: 'trick', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function edit(
        Request $request,
        Trick $trick,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {

        $form = $this->createForm(TrickType::class, $trick, [
            'is_edit' => true,
        ]);

        $form->handleRequest($request);
                
        if ($form->isSubmitted() && $form->isValid()) {

           if (!$this->isCsrfTokenValid('edit' . $trick->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }
                  
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trick->setSlug(
                $slugger->slug($trick->getName())->lower()
            );

            // MEDIA HANDLING (SERVICE UNIQUE)
            $this->mediaService->handleMainImage($form, $trick);
            $this->mediaService->handleImages($form, $trick);
            $this->mediaService->handleVideos($form, $trick);

            $em->flush();
        
        
            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'images' =>$trick->getImages(),
            'videos' =>$trick->getVideos(),
            'mainImage' =>$trick->getMainImage(),
        ]);
    }

    #[Route('/profile/trick/{id}/delete', name: 'app_trick_delete', methods: ['POST'])]
    #[IsGranted('TRICK_DELETE', subject: 'trick', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(
        Trick $trick,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $em->remove($trick);
            $em->flush();
        }
        
        $referer = $request->headers->get('referer');

        if ($referer && str_contains($referer, $request->getSchemeAndHttpHost())) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_profile');

    }

    #[Route('/{id}/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(
        Trick $trick,
        Request $request,
        EntityManagerInterface $em,
        CommentRepository $commentRepository
    ): Response {

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 3;

        $comments = $commentRepository->findPaginatedByTrick($trick, $page, $limit);
        $total = $commentRepository->countByTrick($trick);
        $totalPages = ceil($total / $limit);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $comment->setAuthor($this->getUser());
            $comment->setTrick($trick);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_trick_show', [
                'page' => $page,
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'currentPage' => $page,
            'comments' => $comments,
            'totalPages' => $totalPages,
            'commentForm' => $commentForm->createView(),
        ]);
    }

}