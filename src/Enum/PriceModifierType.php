<?php

namespace Core\Enum;

enum PriceModifierType: string
{
    case AMOUNT = 'amount';
    case PERCENTAGE = 'percentage';
}
