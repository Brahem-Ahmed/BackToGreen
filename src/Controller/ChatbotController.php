<?php

namespace App\Controller;

use App\Service\EventChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChatbotController extends AbstractController
{
    public function __construct(
        private EventChatbotService $chatbotService
    ) {
    }

    #[Route('/api/chatbot/message', name: 'api_chatbot_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez entrer un message',
            ]);
        }

        // Get chatbot response
        $response = $this->chatbotService->generateResponse($userMessage);

        // Format events for JSON response
        $formattedEvents = [];
        foreach ($response['events'] as $event) {
            $formattedEvents[] = [
                'id' => $event->getId(),
                'titre' => $event->getTitre(),
                'description' => substr($event->getDescription(), 0, 150) . '...',
                'dateDebut' => $event->getDateDebut()?->format('Y-m-d H:i'),
                'lieu' => $event->getLieu(),
                'capaciteMax' => $event->getCapaciteMax(),
            ];
        }

        return $this->json([
            'success' => true,
            'message' => $response['message'],
            'eventCount' => $response['eventCount'],
            'events' => $formattedEvents,
        ]);
    }

    #[Route('/api/chatbot/help', name: 'api_chatbot_help', methods: ['GET'])]
    public function getHelp(): JsonResponse
    {
        return $this->json([
            'message' => $this->chatbotService->getHelpMessage(),
        ]);
    }
}
