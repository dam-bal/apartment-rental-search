<?php

namespace App\Console\Commands;

use App\Events\ApartmentUpdated;
use App\Models\Apartment;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;

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
    public function handle(Dispatcher $eventDispatcher)
    {
        Apartment::query()->with(['priceModifiers'])->chunk(50, function ($apartments) use (
            $eventDispatcher
        ) {
            foreach ($apartments as $apartment) {
                $eventDispatcher->dispatch(new ApartmentUpdated($apartment));
            }
        });
    }
}
