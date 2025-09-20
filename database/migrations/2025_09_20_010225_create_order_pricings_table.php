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
        Schema::create('order_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->decimal('dumpster_min_price', 10, 2)->default(0);
            $table->integer('extra_days')->default(0);
            $table->decimal('extra_day_charge', 10, 2)->default(0);
            $table->integer('over_due_days')->default(0);
            $table->decimal('over_due_charge', 10, 2)->default(0);

            $table->string('weight_unit')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('weight_charge', 10, 2)->default(0);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_pricings');
    }
};
