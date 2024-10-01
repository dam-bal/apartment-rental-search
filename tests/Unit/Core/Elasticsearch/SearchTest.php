<?php

namespace Tests\Unit\Core\Elasticsearch;

use Core\Elasticsearch\FilterType;
use Core\Elasticsearch\Search;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\ElasticsearchQueryBuilder\Builder;
use Spatie\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\NestedQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\RangeQuery;
use Spatie\ElasticsearchQueryBuilder\Queries\TermQuery;
use Spatie\ElasticsearchQueryBuilder\Sorts\NestedSort;
use Spatie\ElasticsearchQueryBuilder\Sorts\Sort;

class SearchTest extends TestCase
{
    /** @var MockObject|Builder */
    private $builderMock;

    protected function setUp(): void
    {
        $this->builderMock = $this->createMock(Builder::class);
    }

    public function testSearchSetsCorrectFromAndSize(): void
    {
        $search = new Search($this->builderMock);

        $this->builderMock
            ->expects($this->once())
            ->method('size')
            ->with(10);

        $this->builderMock
            ->expects($this->once())
            ->method('from')
            ->with(10);

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $search->search(page: 2, perPage: 10);
    }

    public static function searchSetsQueryDataProvider(): Generator
    {
        yield [
            [
                'filter' => [
                    'type' => FilterType::MATCH,
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
                    'type' => FilterType::RANGE,
                    'field' => 'testField',
                ]
            ],
            [
                'filter' => 123,
            ],
            BoolQuery::create()->add(RangeQuery::create('testField')->gte(123))
        ];

        yield [
            [
                'filter1' => [
                    'type' => FilterType::MATCH,
                    'field' => 'testField.test1',
                    'nested' => [
                        'path' => 'testPath',
                        'group' => 'testGroup',
                    ]
                ],
                'filter2' => [
                    'type' => FilterType::MATCH,
                    'field' => 'testField.test2',
                    'nested' => [
                        'path' => 'testPath',
                        'group' => 'testGroup',
                    ]
                ]
            ],
            [
                'filter1' => 'test',
                'filter2' => 'test'
            ],
            BoolQuery::create()->add(
                NestedQuery::create(
                    'testPath',
                    BoolQuery::create()->add(TermQuery::create('testField.test1', 'test'))
                        ->add(TermQuery::create('testField.test2', 'test'))
                )->innerHits(NestedQuery\InnerHits::create('testGroup')->size(1)),
                'filter'
            )
        ];

        yield [
            [
                'filter1' => [
                    'type' => FilterType::MATCH,
                    'field' => 'testField.test1',
                    'nested' => [
                        'path' => 'testPath',
                        'group' => 'testGroup1',
                    ]
                ],
                'filter2' => [
                    'type' => FilterType::MATCH,
                    'field' => 'testField.test2',
                    'nested' => [
                        'path' => 'testPath',
                        'group' => 'testGroup2',
                    ]
                ]
            ],
            [
                'filter1' => 'test',
                'filter2' => 'test'
            ],
            BoolQuery::create()->add(
                NestedQuery::create(
                    'testPath',
                    BoolQuery::create()->add(TermQuery::create('testField.test1', 'test'))
                )->innerHits(NestedQuery\InnerHits::create('testGroup1')->size(1)),
                'filter'
            )->add(
                NestedQuery::create(
                    'testPath',
                    BoolQuery::create()->add(TermQuery::create('testField.test2', 'test'))
                )->innerHits(NestedQuery\InnerHits::create('testGroup2')->size(1)),
                'filter'
            )
        ];
    }

    #[DataProvider('searchSetsQueryDataProvider')]
    public function testSearchSetsQuery(array $config, array $parameters, BoolQuery $expectedQuery): void
    {
        $sut = new Search($this->builderMock, $config);

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
            ->method('fields')
            ->willReturnSelf();

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search($parameters);
    }

    public function testSearchSetsSort(): void
    {
        $sut = new Search(
            $this->builderMock,
            [],
            [
                'sort1' => [
                    'field' => 'sort1_field',
                ],
                'sort2' => [
                    'field' => 'sort2_field',
                ]
            ]
        );

        $this->builderMock
            ->expects($this->once())
            ->method('addSort')
            ->with(
                Sort::create('sort1_field', 'asc'),
            );

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search(sort: ['sort1:asc']);
    }

    public function testSearchSetsNestedSort(): void
    {
        $sut = new Search(
            $this->builderMock,
            [],
            [
                'sort1' => [
                    'field' => 'sort1_field',
                    'nested' => [
                        'path' => 'test',
                    ]
                ],
            ]
        );

        $this->builderMock
            ->expects($this->once())
            ->method('addSort')
            ->with(
                NestedSort::create('test', 'sort1_field', 'asc')->maxChildren(1),
            );

        $this->builderMock
            ->method('search')
            ->willReturn($this->createMock(Elasticsearch::class));

        $sut->search(sort: ['sort' => 'sort1:asc']);
    }


    public function testSearchThrowsExceptionWhenParameterConfigDoesNotHaveType(): void
    {
        $sut = new Search(
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
        $sut = new Search(
            $this->builderMock,
            [
                'filter' => [
                    'type' => FilterType::MATCH,
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
