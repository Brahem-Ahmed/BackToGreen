<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Service\PasswordResetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already logged in, redirect to homepage
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // This method will be intercepted by Symfony's logout handler
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        PasswordResetService $passwordResetService,
        EmailService $emailService,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $submitted = false;
        $emailSent = false;
        $email = '';

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email', '');
            $submitted = true;

            $resetToken = $passwordResetService->requestReset($email);
            if ($resetToken) {
                $emailSent = true;
                
                // Generate the reset URL
                $resetUrl = $urlGenerator->generate(
                    'app_reset_password',
                    ['token' => $resetToken->getToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                // Send email
                try {
                    $emailService->sendPasswordResetEmail(
                        $resetToken->getUser(),
                        $resetToken->getToken(),
                        $resetUrl
                    );
                } catch (\Exception $e) {
                    // Log error but show user the same message for security
                    error_log('Failed to send password reset email: ' . $e->getMessage());
                }
            }
            
            // Always show the same message (security best practice)
            $this->addFlash('success', 'If this email exists in our system, a password reset link has been sent. Please check your email.');
        }

        return $this->render('auth/forgot_password.html.twig', [
            'submitted' => $submitted,
            'email_sent' => $emailSent,
            'email' => $email,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        PasswordResetService $passwordResetService,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $resetToken = $passwordResetService->getValidResetToken($token);

        if (!$resetToken) {
            $this->addFlash('error', 'This password reset link is invalid or has expired.');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', '');
            $passwordConfirm = $request->request->get('password_confirm', '');

            if (empty($password)) {
                $this->addFlash('error', 'Password is required.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            if ($password !== $passwordConfirm) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            if (strlen($password) < 8) {
                $this->addFlash('error', 'Password must be at least 8 characters long.');
                return $this->redirectToRoute('app_reset_password', ['token' => $token]);
            }

            $user = $resetToken->getUser();
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setMotDePasse($hashedPassword);

            $passwordResetService->resetPassword($resetToken, $hashedPassword);
            $this->addFlash('success', 'Your password has been reset successfully. You can now login with your new password.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'token' => $token,
        ]);
    }
}
