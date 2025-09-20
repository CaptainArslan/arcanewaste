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
        Schema::create('payment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['upfront_full', 'partial_upfront', 'after_completion']);
            $table->decimal('percentage', 5, 2)->nullable();
            // Only required for partial_upfront (e.g., 30.00 = 30%)

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'type', 'percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_options');
    }
};
