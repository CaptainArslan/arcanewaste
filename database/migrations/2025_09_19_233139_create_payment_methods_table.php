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
            $table->string('name');
            $table->string('code')->unique(); // e.g., stripe, paypal, finix
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();

            $table->json('supported_countries')->nullable(); // ["PK","US"]
            $table->json('supported_currencies')->nullable(); // ["PKR","USD"]
            $table->json('supported_payment_types')->nullable(); // ["credit_card","bank_transfer"]

            // fees: store money as integer cents
            $table->decimal('transaction_fee_percentage', 5, 4)->default(0); // e.g. 0.0290
            $table->integer('transaction_fee_fixed')->default(0); // fixed fee in cents
            $table->integer('monthly_fee')->default(0); // cents
            $table->integer('setup_fee')->default(0); // cents

            $table->integer('min_transaction_amount')->default(0); // in cents
            $table->integer('max_transaction_amount')->nullable(); // in cents

            $table->json('api_configuration')->nullable(); // e.g. endpoints, env flags
            $table->json('onboarding_requirements')->nullable(); // list of docs/fields
            $table->json('features')->nullable();

            $table->enum('status', ['active', 'inactive', 'maintenance', 'deprecated'])->default('active');
            $table->boolean('is_popular')->default(false);
            $table->boolean('requires_merchant_onboarding')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['status', 'is_popular']);
            $table->index(['code']);
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
