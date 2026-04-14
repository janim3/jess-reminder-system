<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->boolean('is_advanced')->default(false)->after('frequency_type');
            $table->json('recurrence_rule')->nullable()->after('send_times');
            $table->date('start_date')->nullable()->after('channel');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('timezone')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['is_advanced', 'recurrence_rule', 'start_date', 'end_date', 'timezone']);
        });
    }
};
