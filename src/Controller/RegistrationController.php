<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RoleUser;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // If already logged in, redirect to home
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $errors = [];
        $formData = [
            'nom' => '',
            'prenom' => '',
            'email' => '',
            'telephone' => '',
            'addresse' => '',
        ];

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom', '');
            $prenom = $request->request->get('prenom', '');
            $email = $request->request->get('email', '');
            $telephone = $request->request->get('telephone', '');
            $addresse = $request->request->get('addresse', '');
            $motDePasse = $request->request->get('motDePasse', '');
            $confirmPassword = $request->request->get('confirmPassword', '');

            // Store form data for re-display
            $formData = compact('nom', 'prenom', 'email', 'telephone', 'addresse');

            // Validate input
            if (empty($nom)) {
                $errors[] = 'Last name is required.';
            }
            if (empty($prenom)) {
                $errors[] = 'First name is required.';
            }
            if (empty($email)) {
                $errors[] = 'Email is required.';
            }
            if (empty($telephone)) {
                $errors[] = 'Phone number is required.';
            }
            if (empty($addresse)) {
                $errors[] = 'Address is required.';
            }
            if (empty($motDePasse)) {
                $errors[] = 'Password is required.';
            }
            if ($motDePasse !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            // Check if user already exists
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $errors[] = 'This email address is already registered.';
            }

                if (empty($errors)) {
                try {
                    // Create new user
                    $user = new User();
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
                    $user->setEmail($email);
                    $user->setTelephone($telephone);
                    $user->setAddresse($addresse);
                    $user->setRole(RoleUser::MEMBRE);

                    // Hash password using Symfony's password hasher
                    $hashedPassword = $passwordHasher->hashPassword($user, $motDePasse);
                    $user->setMotDePasse($hashedPassword);                    // Validate user entity
                    $violations = $validator->validate($user);
                    if (count($violations) > 0) {
                        foreach ($violations as $violation) {
                            $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
                        }
                    } else {
                        // Save user
                        try {
                            $entityManager->persist($user);
                            $entityManager->flush();

                            $this->addFlash('success', 'Registration successful! You can now login.');
                            return $this->redirectToRoute('app_login');
                        } catch (\Exception $flushError) {
                            $errors[] = 'Database error: ' . $flushError->getMessage();
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = 'An error occurred during registration: ' . $e->getMessage();
                }
            }
        }

        return $this->render('auth/register.html.twig', [
            'errors' => $errors,
            'formData' => $formData,
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // if user is already logged in, redirect to events page
        if ($this->getUser()) {
            return $this->redirectToRoute('app_front_events');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);
            
            // Set default role as MEMBRE
            $user->setRole(RoleUser::MEMBRE);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
