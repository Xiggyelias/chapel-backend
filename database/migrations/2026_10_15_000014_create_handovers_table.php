<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('academic_year'); // e.g. 2026/2027
            $table->foreignId('outgoing_president_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('outgoing_president_name');
            $table->string('incoming_president_name');
            $table->string('incoming_president_email');
            $table->string('incoming_president_phone')->nullable();
            $table->text('checklist_running_activities')->nullable();
            $table->text('checklist_pending_items')->nullable();
            $table->text('checklist_key_contacts')->nullable();
            $table->text('checklist_yearly_calendar')->nullable();
            $table->boolean('folders_verified')->default(false);
            $table->foreignId('folders_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('folders_verified_at')->nullable();
            $table->date('meeting_date')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->enum('status', ['initiated', 'checklist_completed', 'folders_verified', 'meeting_scheduled', 'completed'])->default('initiated');
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handovers');
    }
};
