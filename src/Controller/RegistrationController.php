<?php
// src/Controller/RegistrationController.php

/*
 * This file is to handle user registration logic.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
*/
 
namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier
    ) {}

    /**
     * @Route("/inscription", name="app_register")
     */
    #[Route('/inscription', name: 'app_register')]
    public function register(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

        // Prevent logged user from accessing register page
        if ($this->getUser()) {
            $this->addFlash('info', 'Vous êtes déjà connecté.');
            return $this->redirectToRoute('app_home');
        }

        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();

                // Hash password
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );

                // Default values
                $user->setIsVerified(false);
                $user->setCreatedAt(new \DateTimeImmutable());

                $entityManager->persist($user);
                $entityManager->flush();

                // Send email confirmation
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('noreply@snowtricks.com', 'SnowTricks'))
                        ->to((string) $user->getEmail())
                        ->subject('Confirmation de votre email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                $this->addFlash(
                    'success',
                    'Inscription réussie ! 📩 Un email de confirmation vous a été envoyé. Veuillez vérifier votre boîte mail pour activer votre compte.'
                );

                return $this->redirectToRoute('app_login');

            } catch (\Exception $e) {

                $this->addFlash(
                    'danger',
                    'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.'
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Verify email address
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserRepository $userRepository
    ): Response {

        $id = $request->query->get('id');

        if (!$id) {
            $this->addFlash('danger', 'Lien de vérification invalide.');
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);

        } catch (VerifyEmailExceptionInterface $exception) {

            $this->addFlash(
                'danger',
                $translator->trans($exception->getReason(), [], 'VerifyEmailBundle')
            );

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash(
            'success',
            'Votre email a été vérifié avec succès. Vous pouvez maintenant vous connecter.'
        );

        return $this->redirectToRoute('app_login');
    }
}