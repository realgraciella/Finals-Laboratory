<?php

class Request
{
    private $apiKey = 'c3cc075edd114626843f8ae1ef2fc599';

    public function getPlaceDetails($place)
    {
        $url = 'https://api.geoapify.com/v1/geocode/search?text=' . urlencode($place) . '&apiKey=' . $this->apiKey;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        // Decode JSON response
        $response = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($response['features'])) {
            return null;
        }

        // Process the response to get location info
        $location = $this->extractLocation($response);

        return $location;
    }

    private function extractLocation($response)
    {
        $features = $response['features'][0] ?? null;
        if (!$features) {
            return null;
        }

        $properties = $features['properties'] ?? [];
        $geometry = $features['geometry'] ?? [];
        $coordinates = $geometry['coordinates'] ?? [];

        $cityName = $properties['city'] ?? 'Unknown';
        $region = $properties['state'] ?? 'Unknown';

        $cityToRegion = [
            'Manila' => 'National Capital Region',
            'Cebu City' => 'Central Visayas',
            'Davao City' => 'Davao Region',
        ];

        $region = $cityToRegion[$cityName] ?? $region;

        return [
            'region' => $region,
            'coordinates' => $coordinates,
        ];
    }

    public function getIslandFromRegion($region)
    {
        // Define a mapping from region to island
        $regionsToIslands = [
            'National Capital Region' => 'Luzon',
            'Central Luzon' => 'Luzon',
            'CALABARZON' => 'Luzon',
            'MIMAROPA' => 'Luzon',
            'Bicol' => 'Luzon',
            'Central Visayas' => 'Visayas',
            'Western Visayas' => 'Visayas',
            'Eastern Visayas' => 'Visayas',
            'Davao Region' => 'Mindanao',
            'Zamboanga Peninsula' => 'Mindanao',
            'Northern Mindanao' => 'Mindanao',
            'SOCCSKSARGEN' => 'Mindanao',
            'Caraga' => 'Mindanao',
        ];

        return $regionsToIslands[$region] ?? 'Unknown Island';
    }
}