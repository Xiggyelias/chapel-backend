<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->foreignId('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('owner')->default('Chapel Sound & Media Department'); // owner department/store
            $table->date('needed_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['requested', 'approved', 'issued', 'returned', 'rejected'])->default('requested');
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_requests');
    }
};
