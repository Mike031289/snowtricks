<?php
// src/Controller/ProfileController.php

/*
 * This file is part of the SnowTricks project.
 *
 * (c) Adjoukou AGBELOU <adjoukouagbelou@gmail.com> Dev-Application PHP Symfony
*/

namespace App\Controller;

use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'tricks' => $user->getTricks(),
        ]);
    }

    #[Route('/profile/avatar', name: 'app_profile_avatar', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateAvatar(
        Request $request,
        EntityManagerInterface $em,
        AvatarService $avatarService
    ): Response {

        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('avatar_upload', $request->request->get('_token'))) {
            return $this->redirectToRoute('app_profile');
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('avatar');

        if ($file) {
            // UPLOAD AVATAR
            $filename = $avatarService->upload($user, $file);

            $user->setAvatar($filename);

            $em->flush();
        }

        return $this->redirectToRoute('app_profile');
    }
}