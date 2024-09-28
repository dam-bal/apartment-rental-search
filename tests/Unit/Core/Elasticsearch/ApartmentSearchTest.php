<?php

namespace Core\Elasticsearch;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\ElasticsearchQueryBuilder\Builder;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\RangeQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\TermQuery;

class ApartmentSearchTest extends TestCase
{
    /** @var MockObject|Builder */
    private $builderMock;

    protected function setUp(): void
    {
        $this->builderMock = $this->createMock(Builder::class);
    }

    public function testSearchSetsSize(): void
    {
        $sut = new ApartmentSearch($this->builderMock, []);

        $this->builderMock
            ->expects($this->once())
            ->method('size')
            ->with(123)
            ->willReturnSelf();

        $this->builderMock
            ->method('from')
            ->willReturnSelf();

        $this->builderMock
            ->method('addQuery')
            ->willReturnSelf();

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search(['perPage' => 123]);
    }

    public function testSearchSetsFrom(): void
    {
        $sut = new ApartmentSearch($this->builderMock, []);

        $this->builderMock
            ->method('size')
            ->with(10)
            ->willReturnSelf();

        $this->builderMock
            ->expects($this->once())
            ->method('from')
            ->with(20)
            ->willReturnSelf();

        $this->builderMock
            ->method('addQuery')
            ->willReturnSelf();

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search(['perPage' => 10, 'page' => 2]);
    }

    public static function searchSetsQueryDataProvider(): Generator
    {
        yield [
            [
                'filter' => [
                    'type' => ApartmentFilterType::MATCH,
                    'field' => 'testField',
                ]
            ],
            [
                'filter' => '123',
            ],
            BoolQuery::create()->add(new TermQuery('testField', '123')),
        ];

        yield [
            [
                'filter' => [
                    'type' => ApartmentFilterType::RANGE_MIN,
                    'field' => 'testField',
                ]
            ],
            [
                'filter' => 123,
            ],
            BoolQuery::create()->add(RangeQuery::create('testField')->gte(123))
        ];
    }

    #[DataProvider('searchSetsQueryDataProvider')]
    public function testSearchSetsQuery(array $config, array $parameters, BoolQuery $expectedQuery): void
    {
        $sut = new ApartmentSearch($this->builderMock, $config);

        $this->builderMock
            ->method('size')
            ->willReturnSelf();

        $this->builderMock
            ->method('from')
            ->willReturnSelf();

        $this->builderMock
            ->expects($this->once())
            ->method('addQuery')
            ->with($expectedQuery)
            ->willReturnSelf();

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search($parameters);
    }

    public function testSearchThrowsExceptionWhenParameterConfigDoesNotHaveType(): void
    {
        $sut = new ApartmentSearch(
            $this->builderMock,
            [
                'filter' => [
                    'field' => 'testField',
                ]
            ]
        );

        $this->expectException(InvalidArgumentException::class);

        $sut->search(
            [
                'filter' => '123',
            ]
        );
    }

    public function testSearchThrowsExceptionWhenParameterConfigDoesNotHaveField(): void
    {
        $sut = new ApartmentSearch(
            $this->builderMock,
            [
                'filter' => [
                    'type' => ApartmentFilterType::MATCH,
                ]
            ]
        );

        $this->expectException(InvalidArgumentException::class);

        $sut->search(
            [
                'filter' => '123',
            ]
        );
    }
}
