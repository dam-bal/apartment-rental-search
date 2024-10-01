<?php

namespace App\Http\Controllers;

use App\Events\ApartmentUpdated;
use App\Http\Requests\ApartmentFilterRequest;
use App\Http\Requests\ApartmentPriceRequest;
use App\Models\Apartment;
use Core\Elasticsearch\ApartmentSearch;
use Eloquentity\Eloquentity;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __construct(
        private readonly ApartmentSearch $apartmentSearch,
        private readonly Dispatcher $eventDispatcher,
        private readonly Eloquentity $eloquentity
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

        $entity = $this->eloquentity->map($apartment, \Core\Entity\Apartment::class);

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

        $results = [];

        foreach ($result['hits']['hits'] as $hit) {
            $resultItem = $hit['_source'];

            foreach ($hit['inner_hits'] ?? [] as $key => $innerHit) {
                $innerHits = $innerHit['hits']['hits'];

                if (count($innerHits)) {
                    $resultItem[$key] = $innerHit['hits']['hits'][0]['_source'];
                } else {
                    $resultItem[$key] = null;
                }
            }

            $results[] = $resultItem;
        }

        $totalHits = $result['hits']['total']['value'];
        $pages = ceil($totalHits / $request->input('perPage', 12));

        return [
            'total' => $totalHits,
            'pages' => $pages,
            'result' => $results,
        ];
    }
}
