<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HashtagGenerator
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function generateHashtags(string $text): array
    {
        if (empty(trim($text))) {
            return ['#Eco', '#Green', '#Nature', '#Clean', '#BacktoGreen'];
        }

        $prompt = "Génère 8 hashtags écologiques pertinents pour : \"$text\". Réponds seulement avec les hashtags séparés par des espaces.";

        try {
            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/distilgpt2', [
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => ['max_length' => 100],
                ],
                'timeout' => 10,
            ]);

            $result = $response->toArray();
            $generated = $result[0]['generated_text'] ?? '';

            // Extraction des hashtags
            preg_match_all('/#\w+/', $generated, $matches);
            $hashtags = $matches[0] ?? [];

            // Complète si moins de 8
            $default = ['#Eco', '#Green', '#Nature', '#Clean', '#BacktoGreen', '#Tunisie', '#Environnement', '#Recycle'];
            $hashtags = array_merge($hashtags, $default);

            return array_slice(array_unique($hashtags), 0, 8);
        } catch (\Exception $e) {
            // En cas d'erreur, retourne des hashtags par défaut
            return ['#Eco', '#Green', '#Nature', '#Clean', '#BacktoGreen', '#Plage', '#Nettoyage', '#Tunisie'];
        }
    }
}