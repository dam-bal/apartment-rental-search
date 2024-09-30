<?php

namespace Core\Elasticsearch;

class ApartmentPrice implements \JsonSerializable
{
    /**
     * @param string[] $startDates
     */
    public function __construct(
        private array $startDates,
        private readonly int $nights,
        private readonly float $price,
        private readonly float $basePrice,
    ) {
    }

    public function addStartDate(string $date): void
    {
        $this->startDates[] = $date;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'startDate' => $this->startDates,
            'nights' => $this->nights,
            'price' => $this->price,
            'basePrice' => $this->basePrice,
        ];
    }
}
