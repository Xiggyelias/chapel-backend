<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('club_executive'); // chaplain, chapel_assistant, chapel_secretary, club_executive, club_president, campus_life_src
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // e.g. President, Secretary
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn(['role', 'club_id', 'phone', 'position', 'status']);
        });
    }
};
