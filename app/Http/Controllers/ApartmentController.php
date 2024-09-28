<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Core\Elasticsearch\ApartmentSearch;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __construct(
        private readonly ApartmentSearch $apartmentSearch
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

    public function filter(Request $request)
    {
        $result = $this->apartmentSearch->search($request->all())->asArray();

        $sources = array_map(static fn(array $item): array => $item['_source'], $result['hits']['hits']);
        $totalHits = $result['hits']['total']['value'];
        $pages = ceil($totalHits / 10);

        return [
            'total' => $totalHits,
            'pages' => $pages,
            'result' => $sources,
        ];
    }
}
