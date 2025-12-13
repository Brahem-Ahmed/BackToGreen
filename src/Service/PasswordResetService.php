<?php

namespace App\Service;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class PasswordResetService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private PasswordResetRepository $passwordResetRepository,
        private TokenGeneratorInterface $tokenGenerator,
    ) {
    }

    /**
     * Create a password reset token for a user
     */
    public function createResetToken(User $user): PasswordReset
    {
        // Delete any existing valid tokens for this user
        $this->entityManager->createQueryBuilder()
            ->delete(PasswordReset::class, 'pr')
            ->where('pr.user = :user')
            ->andWhere('pr.usedAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $resetToken = new PasswordReset();
        $resetToken->setUser($user);
        $resetToken->setToken(bin2hex($this->tokenGenerator->generateToken()));

        $this->entityManager->persist($resetToken);
        $this->entityManager->flush();

        return $resetToken;
    }

    /**
     * Request a password reset by email
     */
    public function requestReset(string $email): ?PasswordReset
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Don't reveal if email exists (security best practice)
            return null;
        }

        return $this->createResetToken($user);
    }

    /**
     * Validate and get the reset token
     */
    public function getValidResetToken(string $token): ?PasswordReset
    {
        return $this->passwordResetRepository->findValidByToken($token);
    }

    /**
     * Reset the password using a valid token
     */
    public function resetPassword(PasswordReset $resetToken, string $newPassword): bool
    {
        if (!$resetToken->isValid()) {
            return false;
        }

        $user = $resetToken->getUser();
        // Password hashing should be done in the controller
        $user->setMotDePasse($newPassword);
        $resetToken->setUsedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return true;
    }

    /**
     * Clean up expired tokens
     */
    public function cleanupExpiredTokens(): int
    {
        return $this->passwordResetRepository->deleteExpiredTokens();
    }
}
