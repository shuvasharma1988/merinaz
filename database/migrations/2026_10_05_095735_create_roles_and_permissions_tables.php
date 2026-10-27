<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Creates the roles table for Owner, Manager, and Staff designations.
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Manager'
            $table->string('slug')->unique(); // e.g., 'manager'
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Creates the granular permissions table.
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'View Own Appointments'
            $table->string('slug')->unique(); // e.g., 'appointments.view_own'
            $table->string('group')->nullable(); // e.g., 'appointments'
            $table->timestamps();
        });

        // Pivot table connecting users to their assigned roles.
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        // Pivot table connecting roles to their granted permissions.
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
