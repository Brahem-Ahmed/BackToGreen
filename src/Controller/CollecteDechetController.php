<?php

namespace App\Controller;

use App\Entity\CollecteDechet;
use App\Form\CollecteDechetType;
use App\Repository\CollecteDechetRepository;
use App\Repository\ZoneCollecteRepository;
use App\Entity\TypeDechet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/collecte_dechet')]
final class CollecteDechetController extends AbstractController
{
    #[Route(name: 'app_collecte_dechet_index', methods: ['GET'])]
    public function index(CollecteDechetRepository $collecteDechetRepository): Response
    {
        return $this->render('collecte_dechet/index.html.twig', [
            'collecte_dechets' => $collecteDechetRepository->findAll(),
        ]);
    }

    #[Route('/stats', name: 'app_collecte_dechet_stats', methods: ['GET'])]
    public function stats(CollecteDechetRepository $collecteDechetRepository): Response
    {
        // Récupérer totaux groupés par type depuis le repository
        $rows = $collecteDechetRepository->getTotalsByType();

        // Normaliser et ajouter les labels lisibles
        $totals = [];
        foreach ($rows as $row) {
            $typeValue = $row['type'];
            $sum = isset($row['total']) ? (float) $row['total'] : 0.0;

            $label = 'Inconnu';
            $rawValue = null;

            // If Doctrine already returned an enum instance
            if ($typeValue instanceof TypeDechet) {
                $label = $typeValue->label();
                $rawValue = $typeValue->value;
            } elseif (is_string($typeValue) || is_int($typeValue)) {
                // Try to convert scalar to enum
                $rawValue = (string) $typeValue;
                $enum = TypeDechet::tryFrom($rawValue);
                if ($enum instanceof TypeDechet) {
                    $label = $enum->label();
                } else {
                    // Fallback: humanize the raw value
                    $label = (string) $rawValue;
                }
            } elseif (is_array($typeValue) && isset($typeValue['value'])) {
                // Some hydrations may return an array representation
                $rawValue = (string) $typeValue['value'];
                $enum = TypeDechet::tryFrom($rawValue);
                $label = $enum ? $enum->label() : $rawValue;
            } else {
                // Last resort: cast to string if possible
                try {
                    $rawValue = (string) $typeValue;
                    $label = $rawValue;
                } catch (\Throwable $e) {
                    $rawValue = null;
                    $label = 'Inconnu';
                }
            }

            $totals[] = [
                'type' => $rawValue,
                'label' => $label,
                'total' => $sum,
            ];
        }

        // Prepare data for chart (labels + values)
        $chartLabels = array_map(fn($t) => $t['label'], $totals);
        $chartValues = array_map(fn($t) => $t['total'], $totals);

        return $this->render('collecte_dechet/stats.html.twig', [
            'totals' => $totals,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }

    #[Route('/new', name: 'app_collecte_dechet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ZoneCollecteRepository $zoneRepository): Response
    {
        $collecteDechet = new CollecteDechet();
        $form = $this->createForm(CollecteDechetType::class, $collecteDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collecteDechet);
            $entityManager->flush();

            return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        $zones = $zoneRepository->findAll();
        $zonesData = array_map(function ($zone) {
            return [
                'id' => $zone->getId(),
                'nom' => $zone->getNom(),
                'latitude' => $zone->getLatitude(),
                'longitude' => $zone->getLongitude(),
            ];
        }, $zones);

        return $this->render('collecte_dechet/new.html.twig', [
            'collecte_dechet' => $collecteDechet,
            'form' => $form,
            'zones_data' => $zonesData,
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_dechet_show', methods: ['GET'])]
    public function show(CollecteDechet $collecteDechet): Response
    {
        return $this->render('collecte_dechet/show.html.twig', [
            'collecte_dechet' => $collecteDechet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collecte_dechet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CollecteDechet $collecteDechet, EntityManagerInterface $entityManager, ZoneCollecteRepository $zoneRepository): Response
    {
        $form = $this->createForm(CollecteDechetType::class, $collecteDechet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
        }

        $zones = $zoneRepository->findAll();
        $zonesData = array_map(function ($zone) {
            return [
                'id' => $zone->getId(),
                'nom' => $zone->getNom(),
                'latitude' => $zone->getLatitude(),
                'longitude' => $zone->getLongitude(),
            ];
        }, $zones);

        return $this->render('collecte_dechet/edit.html.twig', [
            'collecte_dechet' => $collecteDechet,
            'form' => $form,
            'zones_data' => $zonesData,
        ]);
    }

    #[Route('/{id}', name: 'app_collecte_dechet_delete', methods: ['POST'])]
    public function delete(Request $request, CollecteDechet $collecteDechet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collecteDechet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($collecteDechet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_collecte_dechet_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/zones-coordinates', name: 'app_api_zones_coordinates', methods: ['GET'])]
    public function getZonesCoordinates(ZoneCollecteRepository $zoneRepository): JsonResponse
    {
        $zones = $zoneRepository->findAll();
        $zonesData = array_map(function ($zone) {
            return [
                'id' => $zone->getId(),
                'nom' => $zone->getNom(),
                'latitude' => $zone->getLatitude(),
                'longitude' => $zone->getLongitude(),
            ];
        }, $zones);

        return $this->json(['zones' => $zonesData]);
    }

    #[Route('/api/weather', name: 'app_api_weather', methods: ['GET'])]
    public function proxyWeather(Request $request, CacheInterface $cache): JsonResponse
    {
        $lat = $request->query->get('lat');
        $lon = $request->query->get('lon');
        $units = $request->query->get('units', 'metric');

        if (!$lat || !$lon) {
            return $this->json(['error' => 'Missing lat or lon parameters'], Response::HTTP_BAD_REQUEST);
        }

        // Cache key based on coordinates and units (5 minute TTL)
        $cacheKey = 'weather_' . md5($lat . '_' . $lon . '_' . $units);
        
        try {
            $data = $cache->get($cacheKey, function (ItemInterface $item) use ($lat, $lon, $units) {
                $item->expiresAfter(300); // 5 minutes cache TTL
                
                // Use AccuWeather API
                $apiKey = $_ENV['ACCUWEATHER_API_KEY'] ?? getenv('ACCUWEATHER_API_KEY');
                if (!$apiKey) {
                    throw new \Exception('ACCUWEATHER_API_KEY environment variable not set');
                }
                
                // AccuWeather Locations API to get location key from coordinates
                $locationUrl = sprintf(
                    'https://dataservice.accuweather.com/locations/v1/cities/geoposition/search?apikey=%s&q=%s,%%20%s&details=false',
                    urlencode($apiKey),
                    urlencode($lat),
                    urlencode($lon)
                );
                
                try {
                    $client = HttpClient::create();
                    
                    // Get location key from coordinates
                    $locationResponse = $client->request('GET', $locationUrl, ['timeout' => 10]);
                    if ($locationResponse->getStatusCode() >= 400) {
                        throw new \Exception('Failed to get location key from AccuWeather');
                    }
                    
                    $locationData = json_decode($locationResponse->getContent(false), true);
                    if (!isset($locationData['Key'])) {
                        throw new \Exception('Invalid location data from AccuWeather');
                    }
                    
                    $locationKey = $locationData['Key'];
                    
                    // Get current weather forecast using location key
                    $weatherUrl = sprintf(
                        'https://dataservice.accuweather.com/currentconditions/v1/%s?apikey=%s&details=true&metric=true',
                        urlencode($locationKey),
                        urlencode($apiKey)
                    );
                    
                    $weatherResponse = $client->request('GET', $weatherUrl, ['timeout' => 10]);
                    if ($weatherResponse->getStatusCode() >= 400) {
                        throw new \Exception('Failed to get weather from AccuWeather');
                    }
                    
                    $weatherData = json_decode($weatherResponse->getContent(false), true);
                    
                    return [
                        'location' => $locationData,
                        'current' => $weatherData[0] ?? [],
                    ];
                } catch (\Exception $e) {
                    throw new \Exception('Failed to fetch weather: ' . $e->getMessage());
                }
            });
            
            return $this->json($data);
        } catch (\Exception $e) {
            error_log('Weather proxy error: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
    }
}
