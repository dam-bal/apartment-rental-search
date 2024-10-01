<?php

namespace Core\Elasticsearch\Apartment;

use Carbon\Carbon;
use Core\Apartment\Apartment;
use Core\Apartment\Occupancy;

class ApartmentPriceDocumentsBuilder
{
    private const DAYS = 40;
    private const MIN_NIGHTS = 1;
    private const MAX_NIGHTS = 7;

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

        /** @var array<string, ApartmentPriceDocument> $prices */
        $prices = [];

        while ($current < $endStart) {
            for ($i = $this->minNights; $i <= $this->maxNights; $i++) {
                $start = (clone $current)->setTime(14, 0);
                $end = (clone $current)->setTime(12, 0)->addDays($i);

                if ($this->isOccupied($apartment->getOccupancies(), $start, $end)) {
                    continue;
                }

                $price = $apartment->getPrice($start, $end);

                $key = sprintf(
                    '%s_%s_%s',
                    number_format($price->basePrice, 2, thousands_separator: ''),
                    number_format($price->price, 2, thousands_separator: ''),
                    $i
                );

                if (!isset($prices[$key])) {
                    $prices[$key] = new ApartmentPriceDocument(
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

    /**
     * @param Occupancy[] $occupancies
     */
    private function isOccupied(array $occupancies, Carbon $start, Carbon $end): bool
    {
        foreach ($occupancies as $occupancy) {
            $from = Carbon::instance($occupancy->from)->setTime(14, 0);
            $to = Carbon::instance($occupancy->to)->setTime(12, 0);

            if ($this->isOverlapping($from, $to, $start, $end)) {
                return true;
            }
        }

        return false;
    }

    private function isOverlapping(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        return ($start1 <= $end2) && ($start2 <= $end1);
    }
}
