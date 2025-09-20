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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_option_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed'])->default('pending');
            $table->decimal('initial_total_paid', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);

            $table->boolean('is_wallet_used')->default(false);
            $table->decimal('used_wallet_amount', 10, 2)->default(0);
            $table->decimal('needs_to_pay', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
