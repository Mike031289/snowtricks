<?php
// src/Controller/SecurityController.php

/*
 * This file is to handle security-related actions, specifically user login and logout. It uses Symfony's built-in security features to manage authentication. The login action checks if the user is already authenticated and redirects them if so, while also handling any authentication errors and passing the last username back to the login form for user convenience. The logout action is a placeholder that Symfony intercepts to handle the logout process automatically.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
*/

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils,
    ): Response {

        // If already logged → redirect
        if ($this->getUser()) {
            $this->addFlash('info', '👋 Vous êtes déjà connecté.');
            return $this->redirectToRoute('app_home');
        }

        // Get login error
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            $this->addFlash('danger', '❌ Identifiants invalides.');
        }

        // Last username
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles logout automatically
        throw new \LogicException('This method is intercepted by Symfony.');
    }
}