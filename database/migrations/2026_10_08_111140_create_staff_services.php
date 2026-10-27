<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            // This points to the users table, as your staff are just users with specific roles
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // Prevent duplicate assignments of the same service to the same staff member
            $table->unique(['service_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_services');
    }
};
