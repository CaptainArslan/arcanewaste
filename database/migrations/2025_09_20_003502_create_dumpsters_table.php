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
        Schema::create('dumpsters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dumpster_size_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            $table->string('serial_number')->nullable()->unique(); // company asset tag
            $table->string('status')->default('available');
            $table->string('image')->nullable();
            // available, rented, maintenance, inactive

            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_available')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dumpsters');
    }
};
