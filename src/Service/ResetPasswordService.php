<?php

// src/Service/ResetPasswordService.php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Service responsible for password reset process (sending email).
 *
 * Responsibilities:
 * - sendResetPasswordEmail(): send reset password email if user exists
 */
class ResetPasswordService
{
    public function __construct(
        private UserRepository $userRepository,
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * Handle sending reset password email
     */
    public function sendResetPasswordEmail(string $username): void
    {
        // Find user by username
        $user = $this->userRepository->findOneBy(['username' => $username]);

        // SECURITY: This prevents user enumeration attacks.
        if (!$user) {
            return;
        }

        try {
            // Generate reset token
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Silent fail for security (rate limit, etc.)
            return;
        }

        // Create email
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@snowtricks.com', 'SnowTricks'))
            ->to((string) $user->getEmail())
            ->subject('Password Reset Request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        // Send email
        $this->mailer->send($email);
    }
}
