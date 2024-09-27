<?php

namespace App\Providers;

use Core\Elasticsearch\ApartmentsIndex;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
