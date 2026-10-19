<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('audience')->default('all'); // 'all', specific club_id, or role
            $table->foreignId('target_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->boolean('is_guideline')->default(false); // Can be flagged as official Chapel guideline
            $table->boolean('is_pinned')->default(false);
            $table->date('published_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
