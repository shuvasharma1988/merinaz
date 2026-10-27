<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();

            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', [
                'pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show',
            ])->default('pending');

            $table->enum('booking_source', [
                'online', 'admin', 'phone', 'walk_in',
            ])->default('admin');

            $table->decimal('price', 10, 2);
            $table->text('customer_notes')->nullable();
            $table->text('staff_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
