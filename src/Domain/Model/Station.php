<?php
namespace App\Domain\Model;
class Station
{
    public function __construct(
        public readonly string $shortName,
        public readonly string $longName
    ) {}
    public function __toString(): string
    {
        return $this->shortName;
    }
}
