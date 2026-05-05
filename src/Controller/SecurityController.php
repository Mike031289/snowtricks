<?php

// src/Controller/SecurityController.php

/*
 * This file is to handle security-related actions, specifically user login and logout. It uses Symfony's built-in security features to manage authentication. The login action checks if the user is already authenticated and redirects them if so, while also handling any authentication errors and passing the last username back to the login form for user convenience. The logout action is a placeholder that Symfony intercepts to handle the logout process automatically.
 *
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // redirect to home if already logged in
        if ($this->getUser()) {
            $this->addFlash('info', '👋 Vous êtes déjà connecté.');

            return $this->redirectToRoute('app_home');
        }

        // error is null if no error, otherwise contains the error message
        $error = $authenticationUtils->getLastAuthenticationError();

        // lastUsername contains the last username entered by the user, which can be used to pre-fill the login form
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error, // error is null if no error, otherwise contains the error message
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony intercepte automatiquement cette méthode
        throw new \LogicException('Logout géré automatiquement par Symfony.');
    }
}
