<?php

namespace App\Listeners;

use App\Events\ApartmentUpdated;
use App\Models\Apartment;
use Core\Elasticsearch\Apartment\ApartmentDocument;
use Core\Elasticsearch\Apartment\ApartmentDocumentFactory;
use Core\Elasticsearch\Apartment\ApartmentsIndex;
use Eloquentity\Eloquentity;
use Tests\TestCase;

class UpdateApartmentInElasticsearchTest extends TestCase
{
    public function testHandle(): void
    {
        $eloquentityMock = $this->createMock(Eloquentity::class);

        $apartmentDocumentFactoryMock = $this->createMock(ApartmentDocumentFactory::class);

        $apartmentsIndexMock = $this->createMock(ApartmentsIndex::class);

        $sut = new UpdateApartmentInElasticsearch(
            $apartmentDocumentFactoryMock,
            $eloquentityMock,
            $apartmentsIndexMock
        );

        $event = new ApartmentUpdated($this->createMock(Apartment::class));

        $entityMock = $this->createMock(\Core\Apartment\Apartment::class);

        $entityMock
            ->method('getId')
            ->willReturn('id');

        $documentMock = $this->createMock(ApartmentDocument::class);

        $documentMock
            ->method('jsonSerialize')
            ->willReturn(['name' => 'test']);

        $eloquentityMock
            ->expects($this->once())
            ->method('map')
            ->willReturn($entityMock);

        $apartmentDocumentFactoryMock
            ->expects($this->once())
            ->method('createFromEntity')
            ->with($entityMock)
            ->willReturn($documentMock);

        $apartmentsIndexMock
            ->expects($this->once())
            ->method('index')
            ->with(
                'id',
                [
                    'name' => 'test',
                ]
            );

        $sut->handle($event);
    }
}
