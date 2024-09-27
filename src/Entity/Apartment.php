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
        float $locationLat,
        float $locationLon
    ) {
    }
}
