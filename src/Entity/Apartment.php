<?php

namespace Core\Entity;

use Core\Enum\PriceModifierType;
use DateTime;

class Apartment
{
    /**
     * @param Occupancy[] $occupancies
     * @param PriceModifier[] $priceModifiers
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
        private float $basePricePerNight,
        private array $occupancies,
        private array $priceModifiers
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

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return PriceModifier[]
     */
    public function getPriceModifiers(): array
    {
        return $this->priceModifiers;
    }

    /**
     * @return Occupancy[]
     */
    public function getOccupancies(): array
    {
        return $this->occupancies;
    }

    public function getPrice(DateTime $from, DateTime $to): float
    {
        $nights = $to->diff($from)->days;

        $prices = [];

        for ($i = 0; $i < $nights; $i++) {
            $date = $from->modify(sprintf('%s days', $i));
            $dateString = $date->format('Y-m-d');

            $prices[$dateString] = $this->basePricePerNight;

            foreach ($this->priceModifiers as $priceModifier) {
                if ($date >= $priceModifier->getFrom() && $date <= $priceModifier->getTo()) {
                    $value = 0.0;

                    if ($priceModifier->getType() === PriceModifierType::AMOUNT) {
                        $value = $priceModifier->getValue();
                    }

                    if ($priceModifier->getType() === PriceModifierType::PERCENTAGE) {
                        $modifier = $priceModifier->getValue() / 100;

                        $value = $prices[$dateString] * $modifier;
                    }

                    $prices[$dateString] += $value;
                }
            }
        }

        $total = 0;

        foreach ($prices as $price) {
            $total += $price;
        }

        return $total;
    }
}
