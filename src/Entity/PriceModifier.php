<?php

namespace Core\Entity;

use Core\Enum\PriceModifierType;
use DateTime;

class PriceModifier
{
    public function __construct(
        private readonly DateTime $from,
        private readonly DateTime $to,
        private readonly PriceModifierType $type,
        private readonly float $value
    ) {
    }

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function getTo(): DateTime
    {
        return $this->to;
    }

    public function getType(): PriceModifierType
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
