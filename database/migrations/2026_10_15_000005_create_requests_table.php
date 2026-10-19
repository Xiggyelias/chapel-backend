<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique(); // e.g. REQ-2026-0001
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('category', ['event', 'venue', 'poster', 'equipment', 'outing']);
            $table->string('title');
            $table->text('purpose');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('venue_other')->nullable();
            $table->integer('expected_attendance')->nullable()->default(0);
            $table->decimal('budget', 12, 2)->nullable()->default(0.00);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'needs_changes', 'approved', 'declined'])->default('submitted');
            $table->text('decision_reason')->nullable();
            $table->boolean('vc_go_ahead_needed')->default(false);
            $table->enum('vc_go_ahead_status', ['not_needed', 'pending', 'received'])->default('not_needed');
            $table->text('vc_go_ahead_note')->nullable();
            $table->string('vc_go_ahead_attachment')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->boolean('is_late_submission')->default(false); // flagged if submitted < 14 days before event_date
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
