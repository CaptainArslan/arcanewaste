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
        Schema::create('latest_locations', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation: can belong to Driver, Dumpster, Truck, etc.
            $table->morphs('locatable'); // locatable_id, locatable_type

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('address')->nullable();   // optional reverse-geocode
            $table->timestamp('recorded_at')->nullable(); // when this location was recorded

            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest_locations');
    }
};
