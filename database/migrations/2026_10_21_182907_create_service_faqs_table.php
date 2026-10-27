<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('faq_id')->constrained('faqs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['service_id', 'faq_id']);
            $table->index('faq_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_faqs');
    }
};
