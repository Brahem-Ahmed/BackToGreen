<?php

namespace App\Controller;

use App\Repository\EvenementEcologiqueRepository;
use App\Repository\UserRepository;
use App\Repository\ReclamationRepository;
use App\Repository\CollecteDechetRepository;
use App\Repository\ParticipationRepository;
use App\Repository\GroupeRepository;
use App\Repository\GroupeRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackOfficeController extends AbstractController
{
    #[Route('/admin', name: 'app_backoffice')]
    public function index(
        EvenementEcologiqueRepository $eventRepo,
        UserRepository $userRepo,
        ReclamationRepository $reclamationRepo,
        CollecteDechetRepository $collecteRepo,
        ParticipationRepository $participationRepo,
        GroupeRepository $groupeRepo,
        GroupeRequestRepository $groupeRequestRepo
    ): Response {
        $now = new \DateTime();
        
        // Get statistics
        $allEvents = $eventRepo->findAll();
        $allUsers = $userRepo->findAll();
        
        $stats = [
            'total_events' => count($allEvents),
            'upcoming_events' => count($eventRepo->createQueryBuilder('e')
                ->where('e.dateDebut >= :now')
                ->setParameter('now', $now)
                ->getQuery()->getResult()),
            'total_users' => count($allUsers),
            'total_participations' => count($participationRepo->findAll()),
            'total_reclamations' => count($reclamationRepo->findAll()),
            'pending_reclamations' => count($reclamationRepo->createQueryBuilder('r')
                ->where('r.statut = :statut')
                ->setParameter('statut', 'EN_ATTENTE')
                ->getQuery()->getResult()),
            'total_collections' => count($collecteRepo->findAll()),
            'total_groups' => count($groupeRepo->findAll()),
            'pending_group_requests' => count($groupeRequestRepo->findPendingRequests()),
        ];
        
        // Get recent events
        $recentEvents = $eventRepo->createQueryBuilder('e')
            ->orderBy('e.dateDebut', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
            
        // Get recent reclamations
        $recentReclamations = $reclamationRepo->createQueryBuilder('r')
            ->orderBy('r.dateReclamation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('backoffice/layout.html.twig', [
            'stats' => $stats,
            'recent_events' => $recentEvents,
            'recent_reclamations' => $recentReclamations,
        ]);
    }
}
