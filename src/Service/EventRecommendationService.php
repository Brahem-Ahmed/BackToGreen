<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\EvenementEcologique;
use App\Repository\EvenementEcologiqueRepository;
use App\Repository\ParticipationRepository;

class EventRecommendationService
{
    public function __construct(
        private ParticipationRepository $participationRepository,
        private EvenementEcologiqueRepository $eventRepository
    ) {}

    /**
     * Recommande des événements à un utilisateur basé sur son historique
     */
    public function getRecommendedEvents(User $user, int $limit = 5): array
    {
        // 1. Récupérer l'historique de participation
        $participations = $this->participationRepository->findBy(['idUser' => $user]);
        
        if (empty($participations)) {
            // Nouveaux utilisateurs: événements populaires
            return $this->getPopularEvents($limit);
        }

        // 2. Analyser les préférences de l'utilisateur
        $preferences = $this->analyzeUserPreferences($participations);
        
        // 3. Trouver des événements similaires
        $allEvents = $this->eventRepository->findAll();
        $scored = [];
        
        foreach ($allEvents as $event) {
            // Ignorer les événements passés et ceux où l'utilisateur participe déjà
            if ($this->isEventPast($event) || $this->userAlreadyRegistered($user, $event)) {
                continue;
            }
            
            $score = $this->calculateSimilarityScore($event, $preferences);
            $scored[] = ['event' => $event, 'score' => $score];
        }
        
        // 4. Trier par score et retourner le top
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return array_slice(array_column($scored, 'event'), 0, $limit);
    }

    /**
     * Analyse les préférences de l'utilisateur
     */
    private function analyzeUserPreferences(array $participations): array
    {
        $categories = [];
        $lieux = [];
        $hoursOfDay = [];
        
        foreach ($participations as $participation) {
            $event = $participation->getIdEvenement();
            if (!$event) continue;
            
            // Catégories favorites
            $eventCategories = $event->getCategorie();
            if (is_array($eventCategories)) {
                foreach ($eventCategories as $cat) {
                    $categories[$cat] = ($categories[$cat] ?? 0) + 1;
                }
            } elseif ($eventCategories) {
                $categories[$eventCategories] = ($categories[$eventCategories] ?? 0) + 1;
            }
            
            // Lieux favoris
            $lieu = $event->getLieu();
            if ($lieu) {
                $lieux[$lieu] = ($lieux[$lieu] ?? 0) + 1;
            }
            
            // Horaires préférés
            $dateDebut = $event->getDateDebut();
            if ($dateDebut) {
                $hour = (int) $dateDebut->format('H');
                $hoursOfDay[$hour] = ($hoursOfDay[$hour] ?? 0) + 1;
            }
        }
        
        return [
            'categories' => $categories,
            'lieux' => $lieux,
            'hours' => $hoursOfDay,
        ];
    }

    /**
     * Calcule un score de similarité entre un événement et les préférences
     */
    private function calculateSimilarityScore(EvenementEcologique $event, array $preferences): float
    {
        $score = 0.0;
        
        // Score basé sur les catégories (poids: 40%)
        $eventCategories = $event->getCategorie();
        if (is_array($eventCategories)) {
            foreach ($eventCategories as $cat) {
                if (isset($preferences['categories'][$cat])) {
                    $score += $preferences['categories'][$cat] * 0.4;
                }
            }
        } elseif ($eventCategories && isset($preferences['categories'][$eventCategories])) {
            $score += $preferences['categories'][$eventCategories] * 0.4;
        }
        
        // Score basé sur le lieu (poids: 30%)
        $lieu = $event->getLieu();
        if ($lieu && isset($preferences['lieux'][$lieu])) {
            $score += $preferences['lieux'][$lieu] * 0.3;
        }
        
        // Score basé sur l'horaire (poids: 20%)
        $dateDebut = $event->getDateDebut();
        if ($dateDebut) {
            $hour = (int) $dateDebut->format('H');
            if (isset($preferences['hours'][$hour])) {
                $score += $preferences['hours'][$hour] * 0.2;
            }
        }
        
        // Bonus pour les événements proches dans le temps (poids: 10%)
        $now = new \DateTime();
        $diff = $dateDebut ? $dateDebut->getTimestamp() - $now->getTimestamp() : PHP_INT_MAX;
        $daysUntil = $diff / 86400; // Convertir en jours
        
        if ($daysUntil > 0 && $daysUntil <= 30) {
            $score += (30 - $daysUntil) / 30 * 0.1;
        }
        
        return $score;
    }

    /**
     * Retourne les événements populaires (pour nouveaux utilisateurs)
     */
    private function getPopularEvents(int $limit): array
    {
        $allEvents = $this->eventRepository->findAll();
        $scored = [];
        
        foreach ($allEvents as $event) {
            if ($this->isEventPast($event)) {
                continue;
            }
            
            $participationCount = count($this->participationRepository->findBy(['idEvenement' => $event]));
            $scored[] = ['event' => $event, 'count' => $participationCount];
        }
        
        usort($scored, fn($a, $b) => $b['count'] <=> $a['count']);
        
        return array_slice(array_column($scored, 'event'), 0, $limit);
    }

    /**
     * Vérifie si un événement est passé
     */
    private function isEventPast(EvenementEcologique $event): bool
    {
        $dateFin = $event->getDateFin();
        return $dateFin && $dateFin < new \DateTime();
    }

    /**
     * Vérifie si l'utilisateur est déjà inscrit
     */
    private function userAlreadyRegistered(User $user, EvenementEcologique $event): bool
    {
        $participation = $this->participationRepository->findOneBy([
            'idUser' => $user,
            'idEvenement' => $event,
        ]);
        
        return $participation !== null;
    }

    /**
     * Explique pourquoi un événement est recommandé
     */
    public function getRecommendationReason(User $user, EvenementEcologique $event): string
    {
        $participations = $this->participationRepository->findBy(['idUser' => $user]);
        
        if (empty($participations)) {
            return "Événement populaire pour débuter";
        }
        
        $preferences = $this->analyzeUserPreferences($participations);
        $reasons = [];
        
        // Vérifier les catégories
        $eventCategories = $event->getCategorie();
        if (is_array($eventCategories)) {
            foreach ($eventCategories as $cat) {
                if (isset($preferences['categories'][$cat])) {
                    $reasons[] = "Vous aimez les événements de type '$cat'";
                }
            }
        }
        
        // Vérifier le lieu
        $lieu = $event->getLieu();
        if ($lieu && isset($preferences['lieux'][$lieu])) {
            $reasons[] = "Vous participez souvent à $lieu";
        }
        
        return !empty($reasons) ? implode(' et ', $reasons) : "Événement recommandé pour vous";
    }
}
