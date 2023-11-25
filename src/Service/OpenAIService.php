<?php
// src/Service/OpenAIService.php

namespace App\Service;

class OpenAIService
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getOpenAIResponse(string $clientMessage): string
    {
        // Logique pour interagir avec l'API OpenAI et obtenir une réponse
        // Utilisez la clé API ($this->apiKey) pour authentifier votre requête
        // Remplacez cela par votre propre logique d'appel à l'API OpenAI

        // Exemple simplifié : retournez une réponse factice
        return "Réponse d'OpenAI à '$clientMessage'";
    }
}
