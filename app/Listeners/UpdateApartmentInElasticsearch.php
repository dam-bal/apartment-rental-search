<?php

namespace App\Listeners;

use App\Events\ApartmentUpdated;
use Core\Elasticsearch\ApartmentDocumentFactory;
use Core\Elasticsearch\ApartmentsIndex;
use Core\Entity\Apartment;
use Eloquentity\Eloquentity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ApartmentUpdated $event): void
    {
        $entity = $this->eloquentity->map($event->apartment, Apartment::class);

        $document = $this->apartmentDocumentFactory->createFromEntity($entity);

        $this->apartmentsIndex->update(
            $entity->getId(),
            [
                'doc' => $document->jsonSerialize()
            ]
        );
    }
}
