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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Finix", "Stripe", "PayPal"
            $table->string('slug')->unique(); // e.g., "finix", "stripe", "paypal"
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->json('supported_countries')->nullable(); // Array of country codes
            $table->json('supported_currencies')->nullable(); // Array of currency codes
            $table->json('supported_payment_types')->nullable(); // ["credit_card", "debit_card", "bank_transfer", "digital_wallet"]
            $table->decimal('transaction_fee_percentage', 5, 4)->default(0); // e.g., 2.9% = 0.029
            $table->decimal('transaction_fee_fixed', 10, 2)->default(0); // Fixed fee per transaction
            $table->decimal('monthly_fee', 10, 2)->default(0); // Monthly subscription fee
            $table->decimal('setup_fee', 10, 2)->default(0); // One-time setup fee
            $table->integer('min_transaction_amount')->default(0); // Minimum transaction amount in cents
            $table->integer('max_transaction_amount')->nullable(); // Maximum transaction amount in cents
            $table->json('api_configuration')->nullable(); // API endpoints, keys, etc.
            $table->json('onboarding_requirements')->nullable(); // Required documents, fields, etc.
            $table->json('features')->nullable(); // Available features like recurring payments, refunds, etc.
            $table->enum('status', ['active', 'inactive', 'maintenance', 'deprecated'])->default('active');
            $table->boolean('is_popular')->default(false); // Mark popular payment methods
            $table->boolean('requires_merchant_onboarding')->default(true);
            $table->integer('sort_order')->default(0); // For ordering in UI
            $table->timestamps();
            
            $table->index(['status', 'is_popular']);
            $table->index(['slug']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
