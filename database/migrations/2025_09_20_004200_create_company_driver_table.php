<?php

use App\Enums\EmploymentTypeEnum;
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
        Schema::create('company_driver', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();

            // Driver-specific settings per company
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->json('duty_hours')->nullable();
            // example: {"monday": ["08:00-12:00","13:00-17:00"], "tuesday": [...]}
            $table->boolean('is_active')->default(true);

            $table->string('employment_type')->default(EmploymentTypeEnum::FULL_TIME->value);
            $table->date('hired_at')->nullable();
            $table->date('terminated_at')->nullable();

            // Company-specific fields
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_driver');
    }
};
