<?php

namespace Core\Entity;

class Apartment
{
    public function __construct(
        private string $id,
        private string $name,
        private int $bedrooms,
        private int $bathrooms,
        private int $guests,
        private bool $petsAllowed,
        private float $locationLat,
        private float $locationLon
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBedrooms(): int
    {
        return $this->bedrooms;
    }

    public function getBathrooms(): int
    {
        return $this->bathrooms;
    }

    public function getGuests(): int
    {
        return $this->guests;
    }

    public function isPetsAllowed(): bool
    {
        return $this->petsAllowed;
    }

    public function getLocationLat(): float
    {
        return $this->locationLat;
    }

    public function getLocationLon(): float
    {
        return $this->locationLon;
    }
}
