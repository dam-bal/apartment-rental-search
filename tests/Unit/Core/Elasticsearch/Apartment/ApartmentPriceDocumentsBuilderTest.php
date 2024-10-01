<?php

namespace Core\Elasticsearch\Apartment;

use Carbon\Carbon;
use Core\Apartment\Apartment;
use Core\Apartment\Occupancy;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;

class ApartmentPriceDocumentsBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 9, 30));

        $apartment = Mockery::mock(Apartment::class);

        $sut = new ApartmentPriceDocumentsBuilder(2, 1, 3);

        $apartment
            ->shouldReceive('getPrice')
            ->andReturn(new \Core\Apartment\ApartmentPrice(100.0, 100.0));

        $apartment
            ->shouldReceive('getOccupancies')
            ->andReturn([]);

        self::assertEquals(
            [
                new ApartmentPriceDocument(['2024-09-30', '2024-10-01'], 1, 100.0, 100),
                new ApartmentPriceDocument(['2024-09-30', '2024-10-01'], 2, 100.0, 100),
                new ApartmentPriceDocument(['2024-09-30', '2024-10-01'], 3, 100.0, 100),

            ],
            $sut->build($apartment)
        );

        Carbon::setTestNow();
    }

    public function testBuildDoesNotProduceOccupiedPrices(): void
    {
        Carbon::setTestNow(Carbon::create(2024, 9, 30));

        $apartment = Mockery::mock(Apartment::class);

        $sut = new ApartmentPriceDocumentsBuilder(7, 7, 7);

        $apartment
            ->shouldReceive('getPrice')
            ->andReturn(new \Core\Apartment\ApartmentPrice(100.0, 100.0));

        $apartment
            ->shouldReceive('getOccupancies')
            ->andReturn(
                [
                    new Occupancy(
                        DateTime::createFromFormat('Y-m-d', '2024-10-01'),
                        DateTime::createFromFormat('Y-m-d', '2024-10-03'),
                    )
                ]
            );

        self::assertEquals(
            [
                new ApartmentPriceDocument(
                    [
                        '2024-10-03',
                        '2024-10-04',
                        '2024-10-05',
                        '2024-10-06',
                    ],
                    7,
                    100.0,
                    100
                ),

            ],
            $sut->build($apartment)
        );

        Carbon::setTestNow();
    }
}
