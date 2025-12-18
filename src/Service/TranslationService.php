<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private HttpClientInterface $httpClient;
    
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    
    /**
     * Traduit un texte dans la langue cible en utilisant Google Translate API (gratuit, sans clé)
     */
    public function translate(string $text, string $targetLanguage = 'en', string $sourceLanguage = 'fr'): ?string
    {
        if (empty($text)) {
            return $text;
        }

        try {
            $response = $this->httpClient->request('GET', 'https://translate.googleapis.com/translate_a/single', [
                'query' => [
                    'client' => 'gtx',
                    'sl' => $sourceLanguage,
                    'tl' => $targetLanguage,
                    'dt' => 't',
                    'q' => $text,
                ],
            ]);

            $content = $response->getContent();
            $data = json_decode($content, true);
            
            if (isset($data[0])) {
                $translated = '';
                foreach ($data[0] as $sentence) {
                    if (isset($sentence[0])) {
                        $translated .= $sentence[0];
                    }
                }
                return $translated;
            }
            
            return $text;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner le texte original
            return $text;
        }
    }
    
    /**
     * Traduit un événement dans une langue
     */
    public function translateEvent(object $event, string $targetLanguage = 'en'): array
    {
        return [
            'titre' => $this->translate($event->getTitre(), $targetLanguage),
            'description' => $this->translate($event->getDescription(), $targetLanguage),
            'lieu' => $this->translate($event->getLieu(), $targetLanguage),
        ];
    }
    
    /**
     * Détecte la langue d'un texte
     */
    public function detectLanguage(string $text): ?string
    {
        try {
            return $this->translator->getLastDetectedSource();
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Liste des langues supportées
     */
    public function getSupportedLanguages(): array
    {
        return [
            'en' => 'English',
            'fr' => 'Français',
            'ar' => 'العربية',
            'es' => 'Español',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'zh' => '中文',
            'ja' => '日本語',
        ];
    }
}
