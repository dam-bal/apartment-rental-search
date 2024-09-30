<?php

namespace Core\Entity;

use Core\Enum\PriceModifierType;
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
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-02'),
            100.0,
        ];

        yield 'two nights - no price modifiers' => [
            100.0,
            [],
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-03'),
            200.0,
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
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-02'),
            105.0,
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
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-03'),
            210.0,
        ];
    }

    #[DataProvider('getPriceDataProvider')]
    public function testGetPrice(
        float $basePricePerNight,
        array $priceModifiers,
        DateTime $from,
        DateTime $to,
        float $expectedPrice,
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

    public static function getBasePriceDataProvider(): Generator
    {
        yield 'one night - no price modifiers' => [
            100.0,
            [],
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-02'),
            100.0,
        ];

        yield 'two nights - no price modifiers' => [
            100.0,
            [],
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-03'),
            200.0,
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
                    -20.0
                )
            ],
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-02'),
            110.0,
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
                    -20.0
                )
            ],
            DateTime::createFromFormat('Y-m-d', '2024-11-01'),
            DateTime::createFromFormat('Y-m-d', '2024-11-03'),
            220.0,
        ];
    }

    #[DataProvider('getBasePriceDataProvider')]
    public function testGetBasePrice(
        float $basePricePerNight,
        array $priceModifiers,
        DateTime $from,
        DateTime $to,
        float $expectedPrice,
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
            $apartment->getBasePrice($from, $to)
        );
    }
}
