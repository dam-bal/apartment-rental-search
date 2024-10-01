<?php

namespace App\Listeners;

use App\Events\ApartmentUpdated;
use Core\Apartment\Apartment;
use Core\Elasticsearch\Apartment\ApartmentDocumentFactory;
use Core\Elasticsearch\Apartment\ApartmentsIndex;
use Eloquentity\Eloquentity;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateApartmentInElasticsearch implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ApartmentDocumentFactory $apartmentDocumentFactory,
        private readonly Eloquentity $eloquentity,
        private readonly ApartmentsIndex $apartmentsIndex
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(ApartmentUpdated $event): void
    {
        $entity = $this->eloquentity->map($event->apartment, Apartment::class);

        $document = $this->apartmentDocumentFactory->createFromEntity($entity);

        $this->apartmentsIndex->index(
            $entity->getId(),
            $document->jsonSerialize()
        );
    }
}
