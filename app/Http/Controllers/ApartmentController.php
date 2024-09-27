<?php

namespace App\Http\Controllers;

use App\Models\Apartment;

class ApartmentController extends Controller
{
    public function index()
    {
        return Apartment::query()->paginate(10);
    }

    public function show(string $id)
    {
        return Apartment::query()->findOrFail($id);
    }
}
