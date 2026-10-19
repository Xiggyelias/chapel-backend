<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->constrained('requests')->nullOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('venue_name')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('expected_attendance')->nullable()->default(0);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('is_semester_plan')->default(false);
            $table->string('semester')->nullable(); // e.g. "Semester 1 2026/2027"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
