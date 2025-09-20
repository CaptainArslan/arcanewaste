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
        Schema::create('timings', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation
            $table->morphs('timeable');
            // creates: timeable_id, timeable_type

            $table->string('day_of_week');
            // e.g. "monday", "tuesday" (or numeric 0–6 if you prefer)

            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();

            $table->boolean('is_closed')->default(false);
            // for holidays or closed days

            $table->timestamps();

            $table->unique(['timeable_type', 'timeable_id', 'day_of_week'], 'unique_timeable_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timings');
    }
};
