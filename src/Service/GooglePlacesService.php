<?php

namespace App\Service;

use App\Entity\Ville;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GooglePlacesService
{
    private string $apiKey;

    public function __construct(
        string                               $googlePlacesApiKey,
        private readonly HttpClientInterface $httpClient,

    )
    {
        $this->apiKey = $googlePlacesApiKey;
    }

    public function searchPlaces(string $recherche, Ville $ville): array
    {
        $searchText = $recherche . ' à ' . $ville->getName() . ', France';
        $response = $this->httpClient->request(
            'POST',
            'https://places.googleapis.com/v1/places:searchText',
            ['headers' => [
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $this->apiKey,
                'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.location'
            ],
                'json' => [
                    'textQuery' => $searchText,
                ]
            ]
        );
        $data = $response->toArray();
        return $data['places'] ?? [];
    }
}
