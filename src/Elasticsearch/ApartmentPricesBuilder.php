<?php

namespace Core\Elasticsearch;

use Carbon\Carbon;
use Core\Entity\Apartment;

class ApartmentPricesBuilder
{
    private const DAYS = 3;
    private const MIN_NIGHTS = 1;
    private const MAX_NIGHTS = 4;

    public function __construct(
        private readonly int $maxDays = self::DAYS,
        private readonly int $minNights = self::MIN_NIGHTS,
        private readonly int $maxNights = self::MAX_NIGHTS
    ) {
    }

    public function build(Apartment $apartment): array
    {
        $start = Carbon::now();
        $endStart = Carbon::now()->addDays($this->maxDays);

        $current = (clone $start);

        /** @var array<string, ApartmentPrice> $prices */
        $prices = [];

        while ($current < $endStart) {
            for ($i = $this->minNights; $i <= $this->maxNights; $i++) {
                $start = (clone $current)->setTime(0, 0);
                $end = (clone $current)->setTime(0,0)->addDays($i);

                $price = $apartment->getPrice($start, $end);

                $key = sprintf(
                    '%s_%s_%s',
                    number_format($price->basePrice, 2, thousands_separator: ''),
                    number_format($price->price, 2, thousands_separator: ''),
                    $i
                );

                if (!isset($prices[$key])) {
                    $prices[$key] = new ApartmentPrice(
                        [$start->format('Y-m-d')],
                        $i,
                        $price->price,
                        $price->basePrice
                    );
                } else {
                    $prices[$key]->addStartDate($start->format('Y-m-d'));
                }
            }

            $current->addDay();
        }

        return array_values($prices);
    }
}
