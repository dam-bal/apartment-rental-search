<?php

namespace App\Http\Controllers;

use App\Events\ApartmentUpdated;
use App\Http\Requests\ApartmentFilterRequest;
use App\Http\Requests\ApartmentPriceRequest;
use App\Models\Apartment;
use Core\Elasticsearch\Apartment\ApartmentSearch;
use Core\Elasticsearch\ResponseProcessor;
use Eloquentity\Eloquentity;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __construct(
        private readonly ApartmentSearch $apartmentSearch,
        private readonly Dispatcher $eventDispatcher,
        private readonly Eloquentity $eloquentity,
        private readonly ResponseProcessor $apartmentSearchResultProcessor,
    ) {
    }

    public function show(string $id)
    {
        return Apartment::query()->findOrFail($id);
    }

    public function update(string $id, Request $request)
    {
        $apartment = Apartment::query()->with(['priceModifiers'])->findOrFail($id);

        $apartment?->update($request->all());

        $this->eventDispatcher->dispatch(new ApartmentUpdated($apartment));

        return $apartment;
    }

    public function price(string $id, ApartmentPriceRequest $request)
    {
        $apartment = Apartment::query()->with(['priceModifiers'])->findOrFail($id);

        $entity = $this->eloquentity->map($apartment, \Core\Apartment\Apartment::class);

        $price = $entity->getPrice($request->from(), $request->to());

        return [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'nights' => $request->to()->diffInDays($request->from(), true),
            'price' => $price->price,
            'basePrice' => $price->basePrice,
        ];
    }

    public function filter(ApartmentFilterRequest $request)
    {
        $result = $this->apartmentSearch->search($request->all())->asArray();

        $apartmentSearchResult = $this->apartmentSearchResultProcessor->process($result);

        $pages = ceil($apartmentSearchResult->total / $request->input('perPage', ApartmentSearch::PER_PAGE));

        return [
            'total' => $apartmentSearchResult->total,
            'pages' => $pages,
            'result' => $apartmentSearchResult->results,
        ];
    }
}
