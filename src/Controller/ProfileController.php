<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'tricks' => $user->getTricks(), // cleaner and more efficient
        ]);
    }

    #[Route('/profile/avatar', name: 'app_profile_avatar', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateAvatar(): Response
    {        
        // This method is intentionally left blank as the avatar update logic is handled in the ProfileEditController
        return $this->redirectToRoute('app_profile');
    }
}