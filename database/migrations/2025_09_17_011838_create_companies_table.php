<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('customer_panel_url')->nullable();
            $table->string('logo')->nullable();
            $table->longText('description')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('website')->nullable();
            $table->string('onboarding_status')->default('pending');

            // Finix Payment Gateway Integration
            $table->string('finix_identity_id')->nullable();
            $table->string('finix_merchant_id')->nullable();
            $table->string('finix_onboarding_form_id')->nullable();
            $table->text('finix_onboarding_url')->nullable();
            $table->timestamp('finix_onboarding_url_expired_at')->nullable();
            $table->string('finix_onboarding_status')->nullable();
            $table->text('finix_onboarding_notes')->nullable();
            $table->timestamp('finix_onboarding_completed_at')->nullable();

            // System fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug']);
            $table->index(['onboarding_status']);
            $table->index(['is_active']);
            $table->index(['finix_merchant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
