<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_sheets', function (Blueprint $table) {
            $table->boolean('sign_interpreter_required')->default(false)->after('ushers');
            $table->string('sign_interpreter_name')->nullable()->after('sign_interpreter_required');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->boolean('sign_interpreter_required')->default(false)->after('expected_attendance');
            $table->string('sign_interpreter_name')->nullable()->after('sign_interpreter_required');
        });
    }

    public function down(): void
    {
        Schema::table('service_sheets', function (Blueprint $table) {
            $table->dropColumn(['sign_interpreter_required', 'sign_interpreter_name']);
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['sign_interpreter_required', 'sign_interpreter_name']);
        });
    }
};
