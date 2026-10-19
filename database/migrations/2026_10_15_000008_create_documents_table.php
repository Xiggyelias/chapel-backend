<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('folder'); // constitution, executive_contacts, minutes, programmes_events, reports, finance_documents, posters_media, approvals_correspondence, handover_notes
            $table->integer('year');
            $table->string('title');
            $table->string('standardized_filename'); // e.g. 2026-10-15_FOCUS_Conference-Report.pdf
            $table->string('file_path');
            $table->integer('file_size')->default(0); // in bytes
            $table->string('file_extension', 10)->default('pdf');
            $table->integer('version')->default(1);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
