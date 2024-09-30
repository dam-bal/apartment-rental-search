<?php

namespace Core\Entity;

readonly class ApartmentPrice
{
    public function __construct(public float $basePrice, public float $price)
    {
    }
}
