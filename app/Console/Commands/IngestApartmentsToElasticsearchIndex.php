<?php

namespace App\Console\Commands;

use App\Models\Apartment;
use Core\Elasticsearch\ApartmentDocumentFactory;
use Core\Elasticsearch\ApartmentsIndex;
use Eloquentity\Eloquentity;
use Illuminate\Console\Command;
use Core\Entity\Apartment as ApartmentEntity;

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
    public function handle(
        ApartmentsIndex $apartmentsIndex,
        Eloquentity $eloquentity,
        ApartmentDocumentFactory $apartmentDocumentFactory
    ) {
        Apartment::query()->chunk(100, function ($apartments) use (
            $apartmentsIndex,
            $eloquentity,
            $apartmentDocumentFactory
        ) {
            foreach ($apartments as $apartment) {
                $apartmentEntity = $eloquentity->map($apartment, ApartmentEntity::class);

                $apartmentDocument = $apartmentDocumentFactory->createFromEntity($apartmentEntity);

                $apartmentsIndex->index(
                    $apartment->id,
                    $apartmentDocument->jsonSerialize()
                );
            }
        });
    }
}
