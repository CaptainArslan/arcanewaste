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
        Schema::create('driver_overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_attendance_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours', 5, 2);
            $table->decimal('rate', 10, 2); // hourly rate
            $table->decimal('amount', 10, 2);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_over_times');
    }
};
