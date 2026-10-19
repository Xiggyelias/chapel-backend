<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hymns', function (Blueprint $table) {
            $table->id();
            $table->string('hymnal_name')->default('The United Methodist Hymnal');
            $table->string('hymnal_code')->default('UMH'); // UMH, SHONA, etc.
            $table->string('number')->index();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('tune')->nullable();
            $table->string('meter')->nullable();
            $table->string('scripture_ref')->nullable();
            $table->string('category')->nullable();
            $table->longText('lyrics');
            $table->timestamps();

            $table->unique(['hymnal_code', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hymns');
    }
};
