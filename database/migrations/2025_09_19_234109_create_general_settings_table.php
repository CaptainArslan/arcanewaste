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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();

            $table->morphs('settingable'); // company, driver, customer, etc.

            $table->string('key');
            $table->string('value')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();

            $table->timestamps();

            $table->index(['settingable_id', 'settingable_type']);
            $table->index(['key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
