<?php

namespace App\Domain\Model;

use DateTimeImmutable;

class Route
{
    public function __construct(
        public readonly string $id,
        public readonly string $fromStationId,
        public readonly string $toStationId,
        public readonly string $analyticCode,
        public readonly float $distanceKm,
        /** @var string[] */
        public readonly array $path, // Ordered list of station short names
        public readonly DateTimeImmutable $createdAt
    ) {}

    public function getPathString(): string
    {
        return implode(' -> ', $this->path);
    }
}
