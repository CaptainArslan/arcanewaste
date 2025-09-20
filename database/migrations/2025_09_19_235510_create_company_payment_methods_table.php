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
        Schema::create('company_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();

            // gateway identifiers for the onboarded merchant (strings, nullable until onboarded)
            $table->string('merchant_id')->nullable();
            $table->string('account_id')->nullable(); // e.g., Stripe account id, Finix merchant id
            $table->string('identity_id')->nullable(); // e.g., identity reference in gateway
            $table->enum('status', ['pending', 'in_progress', 'active', 'rejected', 'disabled'])->default('pending');

            $table->json('metadata')->nullable(); // raw response / tokens / KYC info
            $table->timestamp('onboarded_at')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'payment_method_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_payment_methods');
    }
};
