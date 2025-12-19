<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Repository\ParticipationRepository;
use App\Repository\GroupeRepository;
use App\Repository\ReclamationRepository;
use App\Repository\CollecteDechetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile', name: 'app_profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: '')]
    public function index(
        ParticipationRepository $participationRepo,
        GroupeRepository $groupeRepo,
        ReclamationRepository $reclamationRepo,
        CollecteDechetRepository $collecteRepo
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Get user's participations
        $participations = $participationRepo->createQueryBuilder('p')
            ->where('p.idUser = :user')
            ->setParameter('user', $user)
            ->orderBy('p.dateInscription', 'DESC')
            ->getQuery()
            ->getResult();

        // Get user's groups
        $groupes = $groupeRepo->createQueryBuilder('g')
            ->where('g.idCreateur = :user')
            ->setParameter('user', $user)
            ->orderBy('g.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();

        // Get user's reclamations
        $reclamations = $reclamationRepo->createQueryBuilder('r')
            ->where('r.idUser = :user')
            ->setParameter('user', $user)
            ->orderBy('r.dateReclamation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Get user's collections
        $collections = $collecteRepo->createQueryBuilder('c')
            ->where('c.idUser = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateCollecte', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Calculate statistics
        $stats = [
            'total_participations' => count($participations),
            'total_groups' => count($groupes),
            'total_reclamations' => count($user->getReclamations()),
            'total_collections' => count($user->getCollecteDechets()),
            'total_reviews' => count($user->getAvis()),
        ];

        return $this->render('front/dashboard/index.html.twig', [
            'user' => $user,
            'participations' => $participations,
            'groupes' => $groupes,
            'reclamations' => $reclamations,
            'collections' => $collections,
            'stats' => $stats,
        ]);
    }

    #[Route('/edit', name: '_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password update if provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setMotDePasse($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('front/profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
