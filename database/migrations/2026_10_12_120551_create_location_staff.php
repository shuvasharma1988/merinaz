<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            // Assuming staff are stored in the users table based on prior module architecture
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['location_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_staff');
    }
};
