<?php

namespace App\Service;

use App\Entity\User;
use App\Service\PHPMailerService;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private PHPMailerService $phpMailer,
        private Environment $twig,
        private LoggerInterface $logger,
        private string $appName = 'BackToGreen',
        private string $baseUrl = 'http://localhost:8000',
    ) {
    }

    /**
     * Send a password reset email
     */
    public function sendPasswordResetEmail(User $user, string $resetToken, string $resetUrl): bool
    {
        try {
            $context = [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'resetToken' => $resetToken,
                'expiresIn' => '24 hours',
                'appName' => $this->appName,
            ];

            $htmlBody = $this->twig->render('emails/password_reset.html.twig', $context);
            
            $result = $this->phpMailer->send(
                $user->getEmail(),
                $user->getNom() . ' ' . $user->getPrenom(),
                'Reset Your Password - BackToGreen',
                $htmlBody
            );

            if ($result) {
                $this->logger->info('Password reset email sent to: ' . $user->getEmail());
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a welcome email to new users
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            $context = [
                'user' => $user,
                'appName' => $this->appName,
                'baseUrl' => $this->baseUrl,
            ];

            $htmlBody = $this->twig->render('emails/welcome.html.twig', $context);
            
            $result = $this->phpMailer->send(
                $user->getEmail(),
                $user->getNom() . ' ' . $user->getPrenom(),
                'Welcome to BackToGreen!',
                $htmlBody
            );

            if ($result) {
                $this->logger->info('Welcome email sent to: ' . $user->getEmail());
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send welcome email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a generic email
     */
    public function sendEmail(string $to, string $subject, string $template, array $context = []): bool
    {
        try {
            $htmlBody = $this->twig->render($template, $context);
            
            return $this->phpMailer->send($to, $to, $subject, $htmlBody);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }
}
