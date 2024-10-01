<?php

namespace App\Providers;

use Core\Elasticsearch\Apartment\ApartmentFilterType;
use Core\Elasticsearch\Apartment\ApartmentSearch;
use Core\Elasticsearch\Apartment\ApartmentsIndex;
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

        $this->app->bind(ApartmentSearch::class, function () {
            return new ApartmentSearch(
                new Builder($this->app->make(Client::class)),
                [
                    'id' => [
                        'field' => 'id',
                        'type' => ApartmentFilterType::MATCH,
                    ],
                    'bathrooms' => [
                        'field' => 'bathrooms',
                        'type' => ApartmentFilterType::RANGE_MIN,
                    ],
                    'bedrooms' => [
                        'field' => 'bedrooms',
                        'type' => ApartmentFilterType::RANGE_MIN,
                    ],
                    'guests' => [
                        'field' => 'guests',
                        'type' => ApartmentFilterType::RANGE_MIN,
                    ],
                    'petsAllowed' => [
                        'field' => 'petsAllowed',
                        'type' => ApartmentFilterType::MATCH,
                    ],
                    'start' => [
                        'field' => 'prices.startDate',
                        'type' => ApartmentFilterType::MATCH,
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                        ],
                    ],
                    'priceRange' => [
                        'field' => 'prices.price',
                        'type' => ApartmentFilterType::RANGE,
                        'nested' => [
                            'path' => 'prices',
                            'group' => 'pricing',
                        ],
                    ],
                    'nights' => [
                        'field' => 'prices.nights',
                        'type' => ApartmentFilterType::MATCH,
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
