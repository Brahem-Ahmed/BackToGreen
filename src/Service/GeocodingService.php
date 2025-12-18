<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GeocodingService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Géolocaliser une adresse via Nominatim (OpenStreetMap)
     * Retourne latitude, longitude et d'autres détails
     * @param string $address L'adresse à géolocaliser
     * @return array|null ['latitude' => float, 'longitude' => float, ...] ou null si non trouvé
     */
    public function geocode(string $address): ?array
    {
        if (empty(trim($address))) {
            return null;
        }

        try {
            $response = $this->httpClient->request('GET', 'http://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'TN,FR,DZ,MA,LY,EG',
                ],
                'verify_peer' => false,
                'verify_host' => false,
            ]);

            $data = $response->toArray();

            if (empty($data)) {
                return null;
            }

            $result = $data[0];
            return [
                'latitude' => (float) $result['lat'],
                'longitude' => (float) $result['lon'],
                'display_name' => $result['display_name'] ?? null,
                'type' => $result['type'] ?? null,
            ];
        } catch (TransportExceptionInterface $e) {
            // Log error mais pas d'exception - l'app continue
            error_log('Geocoding error: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log('Geocoding error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculer la distance en km entre deux points GPS
     * @param float $lat1 Latitude point 1
     * @param float $lon1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lon2 Longitude point 2
     * @return float Distance en km
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Inverser géocodage: obtenir adresse depuis coordonnées GPS
     * @param float $latitude
     * @param float $longitude
     * @return string|null
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        try {
            $response = $this->httpClient->request('GET', 'http://nominatim.openstreetmap.org/reverse', [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'zoom' => 10,
                ]
            ]);

            $data = $response->toArray();
            return $data['address']['road'] ?? $data['address']['city'] ?? $data['display_name'] ?? null;
        } catch (\Exception $e) {
            error_log('Reverse geocoding error: ' . $e->getMessage());
            return null;
        }
    }
}
