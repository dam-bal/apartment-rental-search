<?php

namespace Tests\Unit\Core\Elasticsearch;

use Core\Elasticsearch\ProcessedResponse;
use Core\Elasticsearch\ResponseProcessor;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ResponseProcessorTest extends TestCase
{
    public static function processDataProvider(): Generator
    {
        yield [
            [],
            new ProcessedResponse(0, [])
        ];

        yield [
            [
                'hits' => [
                    'total' => [
                        'value' => 123,
                    ],
                    'hits' => [
                        [
                            '_source' => [
                                'testField' => 'test',
                                'anotherField' => 123,
                            ],
                        ],
                        [
                            '_source' => [
                                'testField' => 'test',
                                'anotherField' => 321,
                            ],
                        ]
                    ]
                ]
            ],
            new ProcessedResponse(
                123,
                [
                    [
                        'testField' => 'test',
                        'anotherField' => 123,
                    ],
                    [
                        'testField' => 'test',
                        'anotherField' => 321,
                    ]
                ]
            )
        ];

        yield [
            [
                'hits' => [
                    'total' => [
                        'value' => 123,
                    ],
                    'hits' => [
                        [
                            '_source' => [
                                'testField' => 'test',
                                'anotherField' => 123,
                            ],
                            'inner_hits' => [
                                'testInnerHit' => [
                                    'hits' => [
                                        'hits' => [
                                            [
                                                '_source' => [
                                                    'test' => true,
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]
            ],
            new ProcessedResponse(
                123,
                [
                    [
                        'testField' => 'test',
                        'anotherField' => 123,
                        'testInnerHit' => [
                            'test' => true,
                        ]
                    ],
                ]
            )
        ];

        yield [
            [
                'hits' => [
                    'total' => [
                        'value' => 123,
                    ],
                    'hits' => [
                        [
                            '_source' => [
                                'testField' => 'test',
                                'anotherField' => 123,
                            ],
                            'inner_hits' => [
                                'testInnerHit' => [
                                    'hits' => [
                                        'hits' => []
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]
            ],
            new ProcessedResponse(
                123,
                [
                    [
                        'testField' => 'test',
                        'anotherField' => 123,
                        'testInnerHit' => null,
                    ],
                ]
            )
        ];
    }

    #[DataProvider('processDataProvider')]
    public function testProcess(array $elasticsearchResponse, ProcessedResponse $expected): void
    {
        $sut = new ResponseProcessor();

        self::assertEquals(
            $expected,
            $sut->process($elasticsearchResponse)
        );
    }
}
