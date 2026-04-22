<?php
// src/Controller/ResetPasswordController.php

/*
 * This file for handling password reset functionality. It includes actions for requesting a password reset, confirming the request, and resetting the password using a token. The controller uses Symfony's ResetPasswordBundle to manage the reset process, including token generation and validation. It also sends an email to the user with instructions to reset their password.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 * 
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var string $email */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail(
                $email,
                $mailer,
                $translator
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after requesting reset
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Reset password with token
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        ?string $token = null
    ): Response {

        if ($token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

        } catch (ResetPasswordExceptionInterface $e) {

            $this->addFlash('danger', sprintf(
                '%s - %s',
                $translator->trans(
                    ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                    [],
                    'ResetPasswordBundle'
                ),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Form to change password
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Invalidate token
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $plainPassword)
            );

            $this->entityManager->flush();

            // Clean session
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    /**
     * Send reset email
     */
    private function processSendingPasswordResetEmail(
        string $emailFormData,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): RedirectResponse {

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $emailFormData]);

        // Redirect for security (don't reveal if user exists or not)
        if (!$user) {
            $this->addFlash('info', 'Si un compte existe, un email a été envoyé.');
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        } catch (ResetPasswordExceptionInterface $e) {

            $this->addFlash('danger', 'Veuillez vérifier votre boîte de réception ou réessayer plus tard.');

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('mike.agbelou@gmail.com', 'SnowTricks'))
            ->to((string) $user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        // Store token
        $this->setTokenObjectInSession($resetToken);
        
        $this->addFlash('success', 'Un email de réinitialisation vous a été envoyé.');

        return $this->redirectToRoute('app_check_email');
    }
}