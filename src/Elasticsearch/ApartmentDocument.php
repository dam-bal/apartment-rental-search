<?php

namespace Core\Elasticsearch;

use JsonSerializable;

readonly class ApartmentDocument implements JsonSerializable
{
    /**
     * @param ApartmentPrice[] $prices
     */
    public function __construct(
        private string $id,
        private string $name,
        private int $bedrooms,
        private int $bathrooms,
        private int $guests,
        private bool $petsAllowed,
        private float $locationLat,
        private float $locationLon,
        private string $description,
        private array $prices = []
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => ucfirst($this->name) . ' Apartment',
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'guests' => $this->guests,
            'petsAllowed' => $this->petsAllowed,
            'location' => [
                'lat' => $this->locationLat,
                'lon' => $this->locationLon,
            ],
            'description' => $this->description,
            'prices' => $this->prices,
        ];
    }
}
