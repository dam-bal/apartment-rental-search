<?php

namespace Core\Entity;

use DateTime;

readonly class Occupancy
{
    public function __construct(
        public DateTime $from,
        public DateTime $to,
    ) {
    }
}
