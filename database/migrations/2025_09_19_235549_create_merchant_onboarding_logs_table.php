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
        Schema::create('merchant_onboarding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('action')->nullable(); // e.g., "started", "callback_received", "completed", "rejected"
            $table->string('source')->nullable(); // e.g., "stripe", "webhook"
            $table->json('payload')->nullable(); // raw payload / response
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_onboarding_logs');
    }
};
