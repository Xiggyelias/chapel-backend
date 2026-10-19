<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('contact_phone')->nullable()->after('official_email');
            $table->string('founding_president_name')->nullable()->after('current_president');
            $table->string('founding_president_email')->nullable()->after('founding_president_name');
            $table->string('constitution_path')->nullable()->after('founding_president_email');
            $table->text('charter_notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'contact_phone',
                'founding_president_name',
                'founding_president_email',
                'constitution_path',
                'charter_notes',
            ]);
        });
    }
};
