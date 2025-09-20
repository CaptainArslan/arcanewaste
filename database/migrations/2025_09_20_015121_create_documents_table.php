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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation: can belong to Driver, Dumpster, Company, Order, etc.
            $table->morphs('documentable'); // documentable_id, documentable_type

            $table->string('name');                 // e.g. "Driver License", "Contract"
            $table->string('type')->nullable();     // e.g. "pdf", "jpg", "png"
            $table->string('file_path');            // storage path
            $table->string('mime_type')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
