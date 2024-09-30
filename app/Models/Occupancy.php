<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Occupancy extends Model
{
    use HasFactory;

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    protected function casts(): array
    {
        return [
            'from' => 'datetime:Y-m-d H:i:s',
            'to' => 'datetime:Y-m-d H:i:s'
        ];
    }
}
