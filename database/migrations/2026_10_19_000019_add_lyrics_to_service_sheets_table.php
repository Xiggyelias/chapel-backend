<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_sheets', function (Blueprint $table) {
            $table->text('lyrics')->nullable()->after('hymns');
        });
    }

    public function down(): void
    {
        Schema::table('service_sheets', function (Blueprint $table) {
            $table->dropColumn('lyrics');
        });
    }
};
