<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_staff', function (Blueprint $table) {
            $table->id();

            // Link to the services table
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();

            // Link to our new staff_profiles table
            $table->foreignId('staff_profile_id')->constrained()->cascadeOnDelete();

            $table->timestamps();

            // Prevent assigning the same service to a staff member multiple times
            $table->unique(['service_id', 'staff_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_staff');
    }
};
