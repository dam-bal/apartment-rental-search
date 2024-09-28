<?php

namespace App\Providers;

use Core\Elasticsearch\ApartmentFilterType;
use Core\Elasticsearch\ApartmentSearch;
use Core\Elasticsearch\ApartmentsIndex;
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
