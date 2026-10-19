<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('requests')->nullOnDelete();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->integer('attendance');
            $table->text('outcome');
            $table->decimal('spending', 12, 2)->default(0.00);
            $table->json('photos')->nullable();
            $table->enum('status', ['submitted', 'reviewed'])->default('submitted');
            $table->text('chaplaincy_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
