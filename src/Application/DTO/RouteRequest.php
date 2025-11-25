<?php

namespace App\Application\DTO;

// Définition d'un Data Transfer Object (DTO)
// Ce DTO est utilisé par le Serializer de Symfony pour convertir le corps JSON
// de la requête HTTP POST en un objet PHP structuré.

class RouteRequest
{
    /**
     * @param string $fromStationId Identifiant de la station de départ (e.g., "MX")
     * @param string $toStationId Identifiant de la station d'arrivée (e.g., "SCH")
     * @param string $analyticCode Code analytique du type de transport (e.g., "PASSENGER", "FREIGHT")
     */
    public function __construct(
        // Utilisation des propriétés typées promues pour définir la classe
        public string $fromStationId,
        public string $toStationId,
        public string $analyticCode
    ) {
    }
}
