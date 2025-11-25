<?php

namespace App\Application\Service;

use App\Domain\Model\Route;
use App\Infrastructure\DataLoader\RailDataLoader;

class RouteCalculator
{
    public function __construct(private readonly RailDataLoader $dataLoader)
    {
        $this->dataLoader->loadData();
    }

    public function findShortestRoute(
        string $startId, 
        string $endId, 
        string $analyticCode
    ): Route {
        
        // --- Initialization ---
        
        // Distances map: [StationID => shortestDistanceFoundSoFar]
        $distances = array_fill_keys(array_keys($this->dataLoader->getStations()), INF);
        $distances[$startId] = 0;

        // Predecessor map: [StationID => previousStationIDInShortestPath]
        $previous = [];

        // Set of all unvisited nodes (using a simple array for small graph size)
        $unvisited = array_keys($this->dataLoader->getStations());
        
        // --- Core Dijkstra Loop ---
        while (!empty($unvisited)) {
            // Find the unvisited node with the smallest distance (Manual Priority Queue)
            $currentId = null;
            $minDistance = INF;
            foreach ($unvisited as $stationId) {
                if ($distances[$stationId] < $minDistance) {
                    $minDistance = $distances[$stationId];
                    $currentId = $stationId;
                }
            }

            // If no node is reachable, break
            if ($currentId === null || $minDistance === INF) {
                break;
            }

            // Mark current node as visited (remove from unvisited array)
            $unvisited = array_filter($unvisited, fn($id) => $id !== $currentId);

            // If we reached the end, stop the main loop
            if ($currentId === $endId) {
                break; 
            }

            // Check neighbors
            foreach ($this->dataLoader->getSegmentsFrom($currentId) as $segment) {
                $neighborId = $segment->childStationId;
                $newDistance = $distances[$currentId] + $segment->distanceKm;

                // Relaxation step: If a shorter path is found
                if ($newDistance < $distances[$neighborId]) {
                    $distances[$neighborId] = $newDistance;
                    $previous[$neighborId] = $currentId;
                }
            }
        }
        
        // --- Path Reconstruction ---
        
        if (!isset($distances[$endId]) || $distances[$endId] === INF) {
            throw new \Exception("Route not found between $startId and $endId (network not connected or station ID invalid).");
        }

        $path = $this->reconstructPath($startId, $endId, $previous);
        
        // --- Return Result ---
        return new Route(
            // NOTE: ID and createdAt are for the database. 
            // You will need a simple temporary Route class for this step.
            'temp-id-' . uniqid(), 
            $startId, 
            $endId, 
            $analyticCode, 
            $distances[$endId], 
            $path,
            new \DateTimeImmutable()
        );
    }
    
    /**
     * @param string $startId
     * @param string $endId
     * @param array<string, string> $previous
     * @return string[]
     */
    private function reconstructPath(string $startId, string $endId, array $previous): array
    {
        $path = [];
        $at = $endId;
        while (isset($previous[$at])) {
            $path[] = $at;
            $at = $previous[$at];
        }
        $path[] = $startId;
        
        return array_reverse($path);
    }
}
