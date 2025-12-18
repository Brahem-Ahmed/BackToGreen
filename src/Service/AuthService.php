<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\RoleUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthService
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private RequestStack $requestStack,
        EntityManagerInterface $entityManager = null
    ) {
        // Get entity manager from repository if not provided
        $this->entityManager = $entityManager ?? $this->userRepository->getEntityManager();
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    public function register(array $userData, RoleUser $role = RoleUser::MEMBRE): User
    {
        if ($this->userRepository->findOneBy(['email' => $userData['email']])) {
            throw new \Exception('This email address is already registered.');
        }

        $user = new User();
        $user->setNom($userData['nom']);
        $user->setPrenom($userData['prenom']);
        $user->setEmail($userData['email']);
        $user->setTelephone($userData['telephone']);
        $user->setAddresse($userData['addresse']);
        $user->setRole($role);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['motDePasse']);
        $user->setMotDePasse($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function login(string $email, string $password): ?User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return null;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }

        $session = $this->getSession();
        $session->set('user_id', $user->getId());
        $session->set('user_email', $user->getEmail());
        $session->set('user_role', $user->getRole()->value);

        return $user;
    }

    public function logout(): void
    {
        $this->getSession()->invalidate();
    }

    public function isAuthenticated(): bool
    {
        return $this->getSession()->has('user_id');
    }

    public function getCurrentUser(): ?User
    {
        $userId = $this->getSession()->get('user_id');

        if (!$userId) {
            return null;
        }

        return $this->userRepository->find($userId);
    }

    public function hasRole(RoleUser $role): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }

        return $user->getRole() === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleUser::ADMIN);
    }

    public function isModerator(): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }

        return in_array($user->getRole(), [RoleUser::ADMIN, RoleUser::MODERATEUR]);
    }

    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            throw new \Exception('Current password is incorrect.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setMotDePasse($hashedPassword);

        $this->entityManager->flush();

        return true;
    }

    public function resetPassword(string $email, string $newPassword): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setMotDePasse($hashedPassword);

        $this->entityManager->flush();

        return true;
    }

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (isset($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }

        if (isset($data['email'])) {
            $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                throw new \Exception('This email address is already in use.');
            }
            $user->setEmail($data['email']);
        }

        if (isset($data['telephone'])) {
            $user->setTelephone($data['telephone']);
        }

        if (isset($data['addresse'])) {
            $user->setAddresse($data['addresse']);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function emailExists(string $email): bool
    {
        return $this->userRepository->findOneBy(['email' => $email]) !== null;
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function deleteAccount(User $user): bool
    {
        if ($this->getCurrentUser()?->getId() === $user->getId()) {
            $this->logout();
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }
}
