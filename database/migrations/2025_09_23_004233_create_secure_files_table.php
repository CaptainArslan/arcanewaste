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
        Schema::create('secure_files', function (Blueprint $table) {
            $table->id();
            $table->longText('credentials');
            $table->string('content_type');
            $table->string('code');
            $table->timestamps();

            $table->unique(['content_type', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secure_files');
    }
};
