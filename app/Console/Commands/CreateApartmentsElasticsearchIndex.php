<?php

namespace App\Console\Commands;

use Core\Elasticsearch\ApartmentsIndex;
use Illuminate\Console\Command;

class CreateApartmentsElasticsearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-apartments-elasticsearch-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create apartments elasticsearch index';

    /**
     * Execute the console command.
     */
    public function handle(ApartmentsIndex $apartmentsIndex)
    {
        $apartments = file_get_contents(__DIR__ . '/../../../resources/data/elasticsearch/apartments.json');

        $apartmentsIndex->create(json_decode($apartments, true));
    }
}
