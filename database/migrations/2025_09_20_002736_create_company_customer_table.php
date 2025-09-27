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
        Schema::create('company_customer', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_active')->default(true); // Company can deactivate a customer
            $table->boolean('is_delinquent')->default(false); // Mark customer delinquent for this company
            $table->integer('delinquent_days')->default(0); // Track number of overdue days

            $table->timestamps();

            $table->unique(['company_id', 'customer_id']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_customer');
    }
};
