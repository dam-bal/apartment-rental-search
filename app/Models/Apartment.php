<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'bathrooms',
        'bedrooms',
        'guests',
        'pets_allowed',
        'base_price_per_night',
    ];

    public function priceModifiers(): HasMany
    {
        return $this->hasMany(PriceModifier::class);
    }

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class);
    }
}
