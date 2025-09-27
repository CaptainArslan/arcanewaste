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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation -> holidayable_id + holidayable_type
            $table->morphs('holidayable');
            // company_id + "App\Models\Company"
            // driver_id + "App\Models\Driver"

            $table->string('name')->nullable();   // e.g. "Christmas", "Sick Leave"
            $table->date('date')->nullable()->index(); // one-off holiday

            // Recurrence handling (for company holidays)
            $table->string('recurrence_type', 20)->default('none')->index(); // none, weekly, yearly
            $table->unsignedTinyInteger('day_of_week')->nullable()->index(); // for weekly (0=Sunday..6=Saturday)
            $table->string('month_day', 5)->nullable()->index(); // "12-25" for yearly holidays

            $table->string('reason')->nullable(); // for driver leave requests
            $table->string('is_approved')->default('pending')->index(); // approved, pending, rejected

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->index(['holidayable_type', 'holidayable_id', 'date'], 'holiday_owner_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
