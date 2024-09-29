<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->ulid('id');
            $table->string('name');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('guests');
            $table->boolean('pets_allowed');
            $table->float('location_lat');
            $table->float('location_lon');
            $table->text('description');
            $table->float('base_price_per_night');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
