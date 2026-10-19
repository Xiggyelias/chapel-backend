<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_sheets', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->enum('service_type', ['Sunday 9:00 a.m.', 'Wednesday 8:00 a.m.']);
            $table->string('preacher')->nullable();
            $table->string('theme')->nullable();
            $table->string('scripture')->nullable();
            $table->text('hymns')->nullable();
            $table->string('liturgist')->nullable();
            $table->string('bible_reader')->nullable();
            $table->string('choir')->nullable();
            $table->string('praise_worship_team')->nullable();
            $table->text('ushers')->nullable();
            $table->string('media_contact')->nullable();
            $table->enum('status', ['draft', 'ready', 'completed'])->default('draft');
            $table->boolean('preacher_submitted_materials')->default(false);
            $table->boolean('media_notified')->default(false);
            $table->text('order_of_service_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_sheets');
    }
};
