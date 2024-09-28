<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'bathrooms',
        'bedrooms',
        'guests',
        'petsAllowed'
    ];
}
