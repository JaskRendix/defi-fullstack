<?php
namespace App\Domain\Model;
class RailSegment
{
    public function __construct(
        public readonly string $parentStationId, // e.g., "MX"
        public readonly string $childStationId,  // e.g., "CGE"
        public readonly float $distanceKm
    ) {}
}
