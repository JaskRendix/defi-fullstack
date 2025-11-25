<?php

namespace App\Infrastructure\DataLoader;

use App\Domain\Model\RailSegment;
use App\Domain\Model\Station;

class RailDataLoader
{
    private const STATIONS_FILE = 'stations.json';
    private const DISTANCES_FILE = 'distances.json';

    /** @var array<string, Station> */
    private array $stations = [];

    /** @var array<string, RailSegment[]> */
    private array $networkSegments = [];

    public function loadData(): void
    {
        // 1. Load Stations
        $stationsData = json_decode(file_get_contents(self::STATIONS_FILE), true);
        foreach ($stationsData as $data) {
            $station = new Station($data['shortName'], $data['longName']);
            $this->stations[$station->shortName] = $station;
        }

        // 2. Load Segments (Edges)
        $distancesData = json_decode(file_get_contents(self::DISTANCES_FILE), true);
        foreach ($distancesData as $network) {
            foreach ($network['distances'] as $data) {
                // Ensure a station exists before creating a segment
                if (isset($this->stations[$data['parent']], $this->stations[$data['child']])) {
                    $segment = new RailSegment(
                        $data['parent'],
                        $data['child'],
                        $data['distance']
                    );
                    // Add the segment to the network index, keyed by the parent station ID
                    $this->networkSegments[$data['parent']][] = $segment;

                    // IMPORTANT ASSUMPTION: The train network is often bi-directional.
                    // If you assume two-way travel, you MUST add the reverse segment.
                    // DOCUMENT THIS ASSUMPTION.
                    $reverseSegment = new RailSegment(
                        $data['child'],
                        $data['parent'],
                        $data['distance']
                    );
                    $this->networkSegments[$data['child']][] = $reverseSegment;

                }
            }
        }
    }

    /** @return Station[] */
    public function getStations(): array
    {
        return $this->stations;
    }

    /**
     * @return RailSegment[]
     * @param string $stationId The short name of the station (e.g., "MX")
     */
    public function getSegmentsFrom(string $stationId): array
    {
        return $this->networkSegments[$stationId] ?? [];
    }
}
