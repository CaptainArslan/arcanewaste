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
        Schema::create('driver_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_attendance_id')->constrained()->cascadeOnDelete();
            $table->time('break_start');
            $table->time('break_end')->nullable();
            $table->integer('duration_minutes')->nullable(); // auto-calc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_breaks');
    }
};
