<?php
// src/Service/AdresseService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdresseService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function validateAddress(string $address): ?array
    {
        $response = $this->httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
            'query' => [
                'q' => $address,
                'limit' => 1,
                'apiKey' => $this->apiKey,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $content = $response->toArray();
            if (!empty($content['features'])) {
                return $content['features'][0]['properties'];
            }
        }

        return null;
    }
}
