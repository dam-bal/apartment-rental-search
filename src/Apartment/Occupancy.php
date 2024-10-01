<?php

namespace Core\Apartment;

use DateTime;

readonly class Occupancy
{
    public function __construct(
        public DateTime $from,
        public DateTime $to,
    ) {
    }
}
