<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. UMSM, FOCUS, PTL
            $table->enum('type', ['fellowship', 'prayer group', 'choir or worship team', 'service']);
            $table->text('description')->nullable();
            $table->string('meeting_schedule')->nullable();
            $table->string('venue')->nullable();
            $table->string('official_email')->unique();
            $table->string('current_president')->nullable();
            $table->string('color_hex', 7)->default('#002147');
            $table->enum('status', ['active', 'pending_approval', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
