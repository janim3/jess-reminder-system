<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->enum('frequency_type', ['daily_once', 'daily_twice', 'daily_thrice', 'weekly']);
            $table->json('send_times');
            $table->enum('channel', ['sms', 'email', 'both']);
            $table->timestamps();

            $table->index(['contact_id', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
