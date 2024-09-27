<?php

namespace App\Console\Commands;

use App\Models\Apartment;
use Core\Elasticsearch\ApartmentsIndex;
use Illuminate\Console\Command;

class IngestApartmentsToElasticsearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ingest-apartments-to-elasticsearch-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest apartments to elasticsearch index';

    /**
     * Execute the console command.
     */
    public function handle(ApartmentsIndex $apartmentsIndex)
    {
        Apartment::query()->chunk(100, function ($apartments) use ($apartmentsIndex) {
            foreach ($apartments as $apartment) {
                $apartmentsIndex->index(
                    $apartment->id,
                    [
                        'id' => $apartment->id,
                        'name' => $apartment->name,
                        'bedrooms' => $apartment->bedrooms,
                        'bathrooms' => $apartment->bathrooms,
                        'guests' => $apartment->guests,
                        'petsAllowed' => (bool)$apartment->pets_allowed,
                        'location' => [
                            'lat' => $apartment->location_lat,
                            'lon' => $apartment->location_lon,
                        ]
                    ]
                );
            }
        });
    }
}
