<?php

namespace Core\Apartment;

use Carbon\Carbon;

class Apartment
{
    private const LOWEST_PRICE_PER_NIGHT = 15.0;

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
     * @return Occupancy[]
     */
    public function getOccupancies(): array
    {
        return $this->occupancies;
    }

    public function getPrice(Carbon $from, Carbon $to): ApartmentPrice
    {
        $nights = $to->setTime(0, 0)->diffInDays($from->setTime(0, 0), true);

        $prices = [];
        $basePrices = [];

        for ($i = 0; $i < $nights; $i++) {
            $date = (clone $from)->addDays($i)->setTime(0, 0);
            $dateString = $date->format('Y-m-d');

            $prices[$dateString] = $this->basePricePerNight;
            $basePrices[$dateString] = $this->basePricePerNight;

            foreach ($this->priceModifiers as $priceModifier) {
                if ($date >= $priceModifier->getFrom()->setTime(0, 0)
                    && $date <= $priceModifier->getTo()->setTime(0, 0)
                ) {
                    $value = match ($priceModifier->getType()) {
                        PriceModifierType::AMOUNT => $priceModifier->getValue(),
                        PriceModifierType::PERCENTAGE => $this->basePricePerNight * ($priceModifier->getValue() / 100),
                    };

                    $prices[$dateString] += $value;

                    if ($value > 0) {
                        $basePrices[$dateString] += $value;
                    }
                }
            }
        }

        $priceTotal = 0;
        foreach ($prices as $price) {
            $priceTotal += max($price, self::LOWEST_PRICE_PER_NIGHT);
        }

        $basePriceTotal = array_sum(array_values($basePrices));

        return new ApartmentPrice($basePriceTotal, $priceTotal);
    }
}
