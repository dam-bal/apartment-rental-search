<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Occupancy;
use App\Models\PriceModifier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Apartment::factory()
            ->has(PriceModifier::factory()->count(3))
            ->has(Occupancy::factory()->count(3))
            ->count(500)
            ->create();
    }
}
