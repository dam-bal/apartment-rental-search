<?php

namespace Core\Elasticsearch;

use Core\Entity\Apartment;

class ApartmentDocumentFactory
{
    public function __construct(private readonly ApartmentPricesBuilder $apartmentPricesBuilder)
    {
    }

    public function createFromEntity(Apartment $apartment): ApartmentDocument
    {
        return new ApartmentDocument(
            $apartment->getId(),
            $apartment->getName(),
            $apartment->getBedrooms(),
            $apartment->getBathrooms(),
            $apartment->getGuests(),
            $apartment->isPetsAllowed(),
            $apartment->getLocationLat(),
            $apartment->getLocationLon(),
            $apartment->getDescription(),
            $this->apartmentPricesBuilder->build($apartment)
        );
    }
}
