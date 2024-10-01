<?php

namespace Core\Apartment;

use Carbon\Carbon;
use DateTime;

readonly class Occupancy
{
    public function __construct(
        public DateTime $from,
        public DateTime $to,
    ) {
        $this->from->setTime(14, 0);
        $this->to->setTime(12, 0);
    }

    public function isOverlapping(Carbon $start, Carbon $end): bool
    {
        return ($start <= $this->to) && ($this->from <= $end);
    }
}
