<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['service_id']);
            $table->dropForeign(['staff_id']);

            // Drop columns migrated to appointment_items
            $table->dropColumn(['service_id', 'staff_id', 'start_time', 'end_time', 'price']);

            // Add subtotal to track the sum of items before tax/discount
            $table->decimal('subtotal', 10, 2)->default(0)->after('booking_source');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->dropColumn('subtotal');
        });
    }
};
