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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dumpster_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('dumpster_size_id')->constrained()->cascadeOnDelete();
            $table->foreignId('waste_type_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'dispatched',
                'delivered',
                'picked_up',
                'cancelled',
                'completed',
            ])->default('pending');

            $table->boolean('return_requested')->default(false);
            $table->boolean('is_early_return')->default(false);
            $table->boolean('is_job_shifted')->default(false);

            $table->enum('order_type', ['new', 'renewal', 'replacement'])->default('new');
            $table->foreignId('reference_order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
