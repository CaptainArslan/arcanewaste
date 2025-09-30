<?php

use App\Enums\EmploymentTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // Personal Details
            $table->string('full_name')->index();
            $table->string('email')->unique()->index();
            $table->string('phone')->index();
            $table->date('dob')->nullable()->index();
            $table->string('gender')->nullable()->index();
            $table->string('profile_picture')->nullable();
            $table->string('license_number')->nullable()->index();
            $table->date('license_expires_at')->nullable()->index();
            $table->string('identity_document')->nullable()->index(); // e.g., CNIC / passport
            $table->date('identity_expires_at')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
