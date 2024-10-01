<?php

namespace App\Providers;

use Core\Elasticsearch\Apartment\ApartmentsIndex;
use Core\Elasticsearch\FilterType;
use Core\Elasticsearch\Search;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Spatie\ElasticsearchQueryBuilder\Builder;

class ElasticsearchProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function () {
            return ClientBuilder::create()->setHosts(config('elasticsearch.hosts'))->build();
        });

        $this->app->bind(ApartmentsIndex::class, function () {
            return new ApartmentsIndex($this->app->make(Client::class));
        });

        $this->app->bind(Search::class, function () {
            return new Search(
                new Builder($this->app->make(Client::class)),
                [
                    'id' => [
                        'field' => 'id',
                        'type' => FilterType::MATCH,
                    ],
                    'bathrooms' => [
                        'field' => 'bathrooms',
                        'type' => FilterType::RANGE,
                    ],
                    'bedrooms' => [
                        'field' => 'bedrooms',
                        'type' => FilterType::RANGE,
                    ],
                    'guests' => [
                        'field' => 'guests',
                        'type' => FilterType::RANGE,
                    ],
                    'petsAllowed' => [
                        'field' => 'petsAllowed',
                        'type' => FilterType::MATCH,
                    ],
                    'start' => [
                        'field' => 'prices.startDate',
                        'type' => FilterType::MATCH,
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                        ],
                    ],
                    'priceRange' => [
                        'field' => 'prices.price',
                        'type' => FilterType::RANGE,
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                        ],
                    ],
                    'nights' => [
                        'field' => 'prices.nights',
                        'type' => FilterType::MATCH,
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                        ]
                    ]
                ],
                [
                    'bedrooms' => [
                        'field' => 'bedrooms',
                        'order' => 'desc',
                    ],
                    'bathrooms' => [
                        'field' => 'bathrooms',
                        'order' => 'desc',
                    ],
                    'guests' => [
                        'field' => 'guests',
                        'order' => 'desc',
                    ],
                    'petsAllowed' => [
                        'field' => 'petsAllowed',
                        'order' => 'desc',
                    ],
                    'price' => [
                        'field' => 'prices.price',
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                            'mode' => 'min',
                        ],
                    ],
                ],
                [
                    'fields' => [
                        'id',
                        'name',
                        'bedrooms',
                        'bathrooms',
                        'guests',
                        'petsAllowed',
                    ],
                ]
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
