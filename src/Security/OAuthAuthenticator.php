<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\RoleUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuthAuthenticator extends OAuth2Authenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private RouterInterface $router,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var \League\OAuth2\Client\Provider\GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                // Try to find existing user
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    // Create new user
                    $user = new User();
                    $user->setEmail($email);
                    $user->setPrenom($googleUser->getFirstName() ?? 'Google');
                    $user->setNom($googleUser->getLastName() ?? 'User');
                    $user->setTelephone('');
                    $user->setAddresse('');
                    $user->setRole(RoleUser::MEMBRE);

                    // Set random password
                    $hashedPassword = $this->passwordHasher->hashPassword(
                        $user,
                        bin2hex(random_bytes(16))
                    );
                    $user->setMotDePasse($hashedPassword);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_home');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}
