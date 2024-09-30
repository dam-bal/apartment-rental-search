<?php

namespace Core\Entity;

class Occupancy
{
    public function __construct(
        private readonly \DateTime $from,
        private readonly \DateTime $to,
    ) {
    }
}
