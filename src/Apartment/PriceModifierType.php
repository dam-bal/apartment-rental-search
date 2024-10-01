<?php

namespace Core\Apartment;

enum PriceModifierType: string
{
    case AMOUNT = 'amount';
    case PERCENTAGE = 'percentage';
}
