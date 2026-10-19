<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('tracking_code', 30)->nullable()->unique()->after('code');
            $table->string('student_id', 50)->nullable()->after('founding_president_name');
            $table->string('advisor_name', 255)->nullable()->after('founding_president_email');
            $table->string('advisor_email', 255)->nullable()->after('advisor_name');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_code',
                'student_id',
                'advisor_name',
                'advisor_email',
            ]);
        });
    }
};
