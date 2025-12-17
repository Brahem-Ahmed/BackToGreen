<?php

namespace App\Service;

use App\Entity\EvenementEcologique;
use App\Repository\EvenementEcologiqueRepository;

class EventChatbotService
{
    public function __construct(
        private EvenementEcologiqueRepository $eventRepository
    ) {
    }

    
    public function findMatchingEvents(string $userInput): array
    {
        $input = strtolower(trim($userInput));
        $matchingEvents = [];
        
        // Get all events
        $allEvents = $this->eventRepository->findAll();
        
        // Keywords mapping for event categories (use internal category keys stored in DB)
        $keywordMap = [
            'cleaning' => ['nettoyage', 'nettoyer', 'déchet', 'déchets', 'trash', 'waste', 'garbage', 'clean'],
            'tree_planting' => ['plantation', 'plant', 'arbre', 'tree', 'reboisement', 'plantation d\'arbres'],
            'education' => ['sensibilisation', 'education', 'sensibiliser', 'formation', 'atelier', 'conférence', 'conference', 'workshop'],
            'recycling' => ['recyclage', 'recycler', 'recycl', 'réutiliser', 'reuse', 'compost'],
            'conservation' => ['conservation', 'protection', 'biodiversité', 'biodiversity', 'faune', 'flore'],
            'other' => ['autre', 'divers']
        ];

        // Extract what user is looking for (category keys)
        $lookingForCategories = [];
        foreach ($keywordMap as $categoryKey => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_stripos($input, $keyword) !== false) {
                    $lookingForCategories[] = $categoryKey;
                    break;
                }
            }
        }

        // Remove duplicates
        $lookingForCategories = array_unique($lookingForCategories);

        // Filter events
        foreach ($allEvents as $event) {
            $eventCategories = $event->getCategorie() ?? [];
            if (!is_array($eventCategories)) {
                $eventCategories = [$eventCategories];
            }

            // Check if event matches user criteria
            $matches = false;

            // If user specified categories, check for matches using internal keys
            if (!empty($lookingForCategories)) {
                foreach ($lookingForCategories as $lookingCategory) {
                    foreach ($eventCategories as $eventCategory) {
                        if (strtolower($eventCategory) === strtolower($lookingCategory)) {
                            $matches = true;
                            break 2;
                        }
                    }
                }
            }

            // If still not matched, try matching title/description keywords
            if (!$matches) {
                $titre = strtolower((string) $event->getTitre());
                $desc = strtolower((string) $event->getDescription());
                if (mb_stripos($titre, $input) !== false || mb_stripos($desc, $input) !== false) {
                    $matches = true;
                }
            }

            // If user didn't specify categories and no title/description match, include upcoming events by default
            if (empty($lookingForCategories) && !$matches) {
                $matches = true;
            }

            if ($matches) {
                $matchingEvents[] = $event;
            }
        }

        return $matchingEvents;
    }

    /**
     * Generate a chatbot response based on user input
     */
    public function generateResponse(string $userInput): array
    {
        $matchingEvents = $this->findMatchingEvents($userInput);
        
        $response = [
            'message' => '',
            'events' => $matchingEvents,
            'eventCount' => count($matchingEvents),
        ];

        // Generate a friendly message
        if (empty($matchingEvents)) {
            $response['message'] = '😔 Désolé, je n\'ai pas trouvé d\'événements correspondant à vos critères. '
                . 'Pouvez-vous préciser ce que vous cherchez ? (nettoyage, plantation, sensibilisation, recyclage, conférence, atelier)';
        } else {
            $count = count($matchingEvents);
            if ($count === 1) {
                $response['message'] = '🎉 Excellent ! J\'ai trouvé ' . $count . ' événement qui vous intéresse !';
            } else {
                $response['message'] = '🎉 Super ! J\'ai trouvé ' . $count . ' événements qui correspondent à vos intérêts !';
            }
        }

        return $response;
    }

    /**
     * Get help message with examples
     */
    public function getHelpMessage(): string
    {
        return 'Bonjour! 👋 Je suis le chatbot d\'assistance pour trouver des événements. '
            . 'Vous pouvez me dire ce que vous cherchez, par exemple: '
            . '"Je veux participer à un événement de nettoyage" ou '
            . '"Je suis intéressé par une conférence écologique". '
            . 'Les catégories disponibles sont: nettoyage, plantation, sensibilisation, recyclage, conférence, atelier.';
    }
}
