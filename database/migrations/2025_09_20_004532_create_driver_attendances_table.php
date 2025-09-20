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
        Schema::create('driver_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            
            $table->date('attendance_date'); 
            $table->time('scheduled_start')->nullable();
            $table->time('scheduled_end')->nullable();
            
            $table->time('actual_start')->nullable();
            $table->time('actual_end')->nullable();
            
            $table->boolean('is_present')->default(false);
            $table->boolean('is_late')->default(false);
            $table->boolean('is_overtime')->default(false);
        
            $table->decimal('regular_hours', 5, 2)->default(0); // e.g. 8.00
            $table->decimal('overtime_hours', 5, 2)->default(0);
        
            $table->decimal('total_pay', 10, 2)->default(0);
        
            $table->timestamps();
            $table->unique(['company_id','driver_id','attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_attendances');
    }
};
