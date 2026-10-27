<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('source')->nullable(); // e.g., walk-in, online, referral
            $table->string('status')->default('active'); // active, inactive, flagged
            $table->string('tag')->nullable(); // e.g., VIP, New
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('sms_consent')->default(false);
            $table->boolean('email_consent')->default(false);
            $table->boolean('has_medical_issue')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
