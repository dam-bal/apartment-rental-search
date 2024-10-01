<?php

namespace Core\Apartment;

readonly class ApartmentPrice
{
    public function __construct(public float $basePrice, public float $price)
    {
    }
}
