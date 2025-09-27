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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dumpster_size_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('title');
            $table->text('description')->nullable();

            // Promotion type
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // e.g., 10% or $20

            // Validity
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Conditions
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable(); // how many times can be used
            $table->integer('used_count')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
