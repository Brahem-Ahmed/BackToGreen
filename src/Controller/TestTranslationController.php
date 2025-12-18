<?php

namespace App\Controller;

use App\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TestTranslationController extends AbstractController
{
    #[Route('/test/translation', name: 'app_test_translation')]
    public function test(TranslationService $translationService): JsonResponse
    {
        $testText = 'Événement écologique de nettoyage';
        
        return $this->json([
            'service_status' => 'working',
            'original' => $testText,
            'translations' => [
                'en' => $translationService->translate($testText, 'en', 'fr'),
                'ar' => $translationService->translate($testText, 'ar', 'fr'),
                'es' => $translationService->translate($testText, 'es', 'fr'),
            ],
        ]);
    }
}
