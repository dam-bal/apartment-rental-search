<?php

namespace App\Http\Controllers;

use App\Events\ApartmentUpdated;
use App\Models\Apartment;
use Carbon\Carbon;
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

    public function index()
    {
        return Apartment::query()->paginate(10);
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

    public function price(string $id, Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $from = Carbon::createFromFormat('Y-m-d', $from);
        $to = Carbon::createFromFormat('Y-m-d', $to);

        $apartment = Apartment::query()->with(['priceModifiers'])->findOrFail($id);

        $entity = $this->eloquentity->map($apartment, \Core\Entity\Apartment::class);

        $price = $entity->getPrice($from, $to);

        return [
            'basePricePerNight' => $apartment->base_price_per_night,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'nights' => $to->diffInDays($from, true),
            'price' => $price->price,
            'basePrice' => $price->basePrice
        ];
    }

    public function filter(Request $request)
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
