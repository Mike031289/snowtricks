<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}/edit', name: 'app_comment_edit')]
    #[IsGranted('COMMENT_EDIT', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        if ($comment->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
             $comment->setCreatedAt(new \DateTimeImmutable());
             $em->persist($comment);
             
            $em->flush();

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $comment->getTrick()->getSlug(),
                'id' => $comment->getTrick()->getId(),
            ]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
     #[IsGranted('COMMENT_DELETE', subject: 'comment', message: 'Vous devrez disposer de droits requis', statusCode: 404)]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        
        if ($this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $request->request->get('_token'))) {

            if ($comment->getAuthor() !== $this->getUser()) {
                throw $this->createAccessDeniedException();
            }

            $slug = $comment->getTrick()->getSlug();

            $em->remove($comment);
            $em->flush();

            return $this->redirectToRoute('app_trick_show', [
                'slug' => $slug,
                'id' => $comment->getTrick()->getId(),
            ]);    
        
        }
        
        return $this->redirectToRoute('app_trick_show', [
            'slug' => $comment->getTrick()->getSlug(),
            'id' => $comment->getTrick()->getId(),
        ]);
    }
}