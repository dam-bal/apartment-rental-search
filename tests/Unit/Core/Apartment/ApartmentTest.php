<?php

namespace Tests\Unit\Core\Apartment;

use Carbon\Carbon;
use Core\Apartment\Apartment;
use Core\Apartment\ApartmentPrice;
use Core\Apartment\PriceModifier;
use Core\Apartment\PriceModifierType;
use DateTime;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ApartmentTest extends TestCase
{
    public static function getPriceDataProvider(): Generator
    {
        yield 'one night - no price modifiers' => [
            100.0,
            [],
            Carbon::createFromFormat('Y-m-d', '2024-11-01'),
            Carbon::createFromFormat('Y-m-d', '2024-11-02'),
            new ApartmentPrice(100.0, 100.0)
        ];

        yield 'two nights - no price modifiers' => [
            100.0,
            [],
            Carbon::createFromFormat('Y-m-d', '2024-11-01'),
            Carbon::createFromFormat('Y-m-d', '2024-11-03'),
            new ApartmentPrice(200.0, 200.0),
        ];

        yield 'one night - price modifier (amount)' => [
            100.0,
            [
                new PriceModifier(
                    DateTime::createFromFormat('Y-m-d', '2024-11-01'),
                    DateTime::createFromFormat('Y-m-d', '2024-11-07'),
                    PriceModifierType::AMOUNT,
                    10.0
                ),
                new PriceModifier(
                    DateTime::createFromFormat('Y-m-d', '2024-11-01'),
                    DateTime::createFromFormat('Y-m-d', '2024-11-07'),
                    PriceModifierType::AMOUNT,
                    -5.0,
                )
            ],
            Carbon::createFromFormat('Y-m-d', '2024-11-01'),
            Carbon::createFromFormat('Y-m-d', '2024-11-02'),
            new ApartmentPrice(110.0, 105.0),
        ];

        yield 'two nights - price modifier (amount)' => [
            100.0,
            [
                new PriceModifier(
                    DateTime::createFromFormat('Y-m-d', '2024-11-01'),
                    DateTime::createFromFormat('Y-m-d', '2024-11-07'),
                    PriceModifierType::AMOUNT,
                    10.0
                ),
                new PriceModifier(
                    DateTime::createFromFormat('Y-m-d', '2024-11-01'),
                    DateTime::createFromFormat('Y-m-d', '2024-11-07'),
                    PriceModifierType::AMOUNT,
                    -5.0,
                )
            ],
            Carbon::createFromFormat('Y-m-d', '2024-11-01'),
            Carbon::createFromFormat('Y-m-d', '2024-11-03'),
            new ApartmentPrice(220.0, 210.0)
        ];

        yield 'two nights - price modifier - only negative price modifier (amount)' => [
            100.0,
            [
                new PriceModifier(
                    DateTime::createFromFormat('Y-m-d', '2024-11-01'),
                    DateTime::createFromFormat('Y-m-d', '2024-11-07'),
                    PriceModifierType::AMOUNT,
                    -5.0,
                )
            ],
            Carbon::createFromFormat('Y-m-d', '2024-11-01'),
            Carbon::createFromFormat('Y-m-d', '2024-11-03'),
            new ApartmentPrice(200.0, 190.0),
        ];
    }

    #[DataProvider('getPriceDataProvider')]
    public function testGetPrice(
        float $basePricePerNight,
        array $priceModifiers,
        Carbon $from,
        Carbon $to,
        ApartmentPrice $expectedPrice
    ): void {
        $apartment = new Apartment(
            'id',
            'name',
            1,
            2,
            3,
            false,
            0.0,
            0.0,
            'test',
            $basePricePerNight,
            [],
            $priceModifiers
        );

        self::assertEquals(
            $expectedPrice,
            $apartment->getPrice($from, $to)
        );
    }
}
