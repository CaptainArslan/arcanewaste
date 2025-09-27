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
        Schema::create('dumpster_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('name'); // e.g., "10 Yard Dumpster"
            $table->string('code')->nullable();
            $table->unique(['company_id', 'code']); // scoped uniqueness
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Rental rules
            $table->integer('min_rental_days')->default(1);
            $table->integer('max_rental_days')->nullable(); // null = unlimited

            // Pricing
            $table->decimal('base_rent', 10, 2);
            $table->decimal('extra_day_rent', 10, 2)->nullable();
            $table->decimal('overdue_rent', 10, 2)->nullable();

            // Capacity
            $table->decimal('volume_cubic_yards', 8, 2)->nullable();
            $table->integer('weight_limit_lbs')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dumpster_sizes');
    }
};
