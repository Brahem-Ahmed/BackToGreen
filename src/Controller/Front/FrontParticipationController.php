<?php

namespace App\Controller\Front;

use App\Entity\Participation;
use App\Entity\EvenementEcologique;
use App\Entity\StatutParticipation;
use App\Repository\EvenementEcologiqueRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/events')]
class FrontParticipationController extends AbstractController
{
    #[Route('/', name: 'app_front_events', methods: ['GET'])]
    public function index(Request $request, EvenementEcologiqueRepository $evenementRepository, ParticipationRepository $participationRepository): Response
    {
        // Get filter parameters
        $filters = [
            'titre' => $request->query->get('titre', ''),
            'categorie' => $request->query->get('categorie', ''),
            'lieu' => $request->query->get('lieu', ''),
            'disponible' => $request->query->get('disponible', ''),
        ];

        // Get events based on filters
        $filteredFilters = array_filter($filters, fn($v) => $v !== null && $v !== '');
        $events = empty($filteredFilters) 
            ? $evenementRepository->findAll() 
            : $evenementRepository->search($filteredFilters);
        
        // Calculate availability for each event
        $eventsWithAvailability = [];
        foreach ($events as $event) {
            // Count participations for this event (excluding cancelled)
            $allParticipations = $participationRepository->findBy(['idEvenement' => $event]);
            $participationsCount = 0;
            foreach ($allParticipations as $p) {
                if ($p->getStatut() && $p->getStatut() !== StatutParticipation::ANNULE) {
                    $participationsCount++;
                }
            }
            
            $isAvailable = $participationsCount < $event->getCapaciteMax();
            $isPast = $event->getDateFin() && $event->getDateFin() < new \DateTime();
            
            $eventsWithAvailability[] = [
                'event' => $event,
                'participationsCount' => $participationsCount,
                'availableSpots' => $event->getCapaciteMax() - $participationsCount,
                'isAvailable' => $isAvailable && !$isPast,
                'isPast' => $isPast,
            ];
        }
        
        // Apply availability filter if requested
        if ($filters['disponible'] === 'oui') {
            $eventsWithAvailability = array_filter($eventsWithAvailability, fn($item) => $item['isAvailable']);
        } elseif ($filters['disponible'] === 'non') {
            $eventsWithAvailability = array_filter($eventsWithAvailability, fn($item) => !$item['isAvailable']);
        }

        return $this->render('front/events/index.html.twig', [
            'events' => $eventsWithAvailability,
            'filters' => $filters,
        ]);
    }

    #[Route('/{id}', name: 'app_front_event_show', methods: ['GET'])]
    public function show(EvenementEcologique $event, ParticipationRepository $participationRepository): Response
    {
        // Count participations for this event (excluding cancelled)
        $allParticipations = $participationRepository->findBy(['idEvenement' => $event]);
        $participationsCount = 0;
        foreach ($allParticipations as $p) {
            if ($p->getStatut() && $p->getStatut() !== StatutParticipation::ANNULE) {
                $participationsCount++;
            }
        }
        
        $isAvailable = $participationsCount < $event->getCapaciteMax();
        $isPast = $event->getDateFin() && $event->getDateFin() < new \DateTime();
        
        return $this->render('front/events/show.html.twig', [
            'event' => $event,
            'participationsCount' => $participationsCount,
            'availableSpots' => $event->getCapaciteMax() - $participationsCount,
            'isAvailable' => $isAvailable && !$isPast,
            'isPast' => $isPast,
        ]);
    }

    #[Route('/{id}/register', name: 'app_front_event_register', methods: ['POST'])]
    public function register(
        Request $request,
        EvenementEcologique $event,
        ParticipationRepository $participationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Count participations for this event (excluding cancelled)
        $allParticipations = $participationRepository->findBy(['idEvenement' => $event]);
        $participationsCount = 0;
        foreach ($allParticipations as $p) {
            if ($p->getStatut() && $p->getStatut() !== StatutParticipation::ANNULE) {
                $participationsCount++;
            }
        }
        
        $isAvailable = $participationsCount < $event->getCapaciteMax();
        $isPast = $event->getDateFin() && $event->getDateFin() < new \DateTime();
        
        if (!$isAvailable || $isPast) {
            $this->addFlash('error', 'Désolé, cet événement n\'est plus disponible pour inscription.');
            return $this->redirectToRoute('app_front_events');
        }

        // For now, we'll need to get the current user
        // You should replace this with actual user authentication
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire à un événement.');
            return $this->redirectToRoute('app_front_events');
        }

        // Check if user is already registered
        $existingParticipation = $participationRepository->findOneBy([
            'idUser' => $user,
            'idEvenement' => $event,
        ]);

        if ($existingParticipation) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('app_front_event_show', ['id' => $event->getId()]);
        }

        // Create participation
        $participation = new Participation();
        $participation->setIdUser($user);
        $participation->setIdEvenement($event);
        $participation->setDateInscription(new \DateTime());
        $participation->setStatut(StatutParticipation::INSCRIT);

        $entityManager->persist($participation);
        $entityManager->flush();

        $this->addFlash('success', 'Félicitations ! Votre inscription a été confirmée.');
        
        return $this->redirectToRoute('app_front_event_show', ['id' => $event->getId()]);
    }

}
