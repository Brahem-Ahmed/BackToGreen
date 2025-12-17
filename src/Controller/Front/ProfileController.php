<?php

namespace App\Controller\Front;

use App\Entity\Participation;
use App\Entity\StatutParticipation;
use App\Repository\ParticipationRepository;
use App\Service\EventRecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_front_profile', methods: ['GET'])]
    public function index(
        ParticipationRepository $participationRepository,
        EventRecommendationService $recommendationService
    ): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à votre profil.');
            return $this->redirectToRoute('app_login');
        }

        // Get all participations for the current user
        $participations = $participationRepository->findBy(
            ['idUser' => $user],
            ['dateInscription' => 'DESC']
        );
        
        // Get AI recommendations
        $recommendedEvents = $recommendationService->getRecommendedEvents($user, 3);

        // Calculate statistics
        $stats = [
            'total' => count($participations),
            'upcoming' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        foreach ($participations as $participation) {
            $event = $participation->getIdEvenement();
            if ($participation->getStatut() === StatutParticipation::ANNULE) {
                $stats['cancelled']++;
            } elseif ($event->getDateFin() && $event->getDateFin() > new \DateTime()) {
                $stats['upcoming']++;
            } else {
                $stats['completed']++;
            }
        }

        return $this->render('front/profile/index.html.twig', [
            'user' => $user,
            'participations' => $participations,
            'stats' => $stats,
            'recommended_events' => $recommendedEvents,
            'recommendation_service' => $recommendationService,
        ]);
    }

    #[Route('/profile/participation/{id}/cancel', name: 'app_front_participation_cancel', methods: ['POST'])]
    public function cancelParticipation(
        Participation $participation,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        
        if (!$user || $participation->getIdUser() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à annuler cette participation.');
            return $this->redirectToRoute('app_front_profile');
        }

        // Check if event hasn't started yet
        $event = $participation->getIdEvenement();
        if ($event->getDateDebut() && $event->getDateDebut() < new \DateTime()) {
            $this->addFlash('error', 'Impossible d\'annuler : l\'événement a déjà commencé.');
            return $this->redirectToRoute('app_front_profile');
        }

        // Check if already cancelled
        if ($participation->getStatut() === StatutParticipation::ANNULE) {
            $this->addFlash('warning', 'Cette participation est déjà annulée.');
            return $this->redirectToRoute('app_front_profile');
        }

        // Cancel participation
        $participation->setStatut(StatutParticipation::ANNULE);
        $entityManager->flush();

        $this->addFlash('success', 'Votre participation a été annulée avec succès.');
        return $this->redirectToRoute('app_front_profile');
    }
}
