<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('student_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role_in_club')->default('Member'); // Member, Committee, Treasurer, etc.
            $table->enum('status', ['active', 'alumni', 'inactive'])->default('active');
            $table->date('joined_date')->nullable();
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('club_member_id')->constrained('club_members')->cascadeOnDelete();
            $table->date('meeting_date');
            $table->enum('status', ['present', 'absent', 'excused'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['club_member_id', 'meeting_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('club_members');
    }
};
