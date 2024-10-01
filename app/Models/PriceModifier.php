<?php

namespace App\Models;

use Core\Apartment\PriceModifierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceModifier extends Model
{
    use HasFactory;

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    protected function casts(): array
    {
        return [
            'from' => 'datetime:Y-m-d',
            'to' => 'datetime:Y-m-d',
            'type' => PriceModifierType::class,
        ];
    }
}
