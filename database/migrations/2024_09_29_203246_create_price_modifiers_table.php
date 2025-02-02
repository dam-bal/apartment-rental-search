<?php

use App\Models\Apartment;
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
        Schema::create('price_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Apartment::class);
            $table->date('from');
            $table->date('to');
            $table->enum('type', ['amount', 'percentage']);
            $table->float('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_modifiers');
    }
};
