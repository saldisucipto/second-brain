<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mom_id')->constrained('moms')->cascadeOnDelete();
            $table->text('action');
            $table->string('pic')->nullable();
            $table->enum('status', ['pending', 'progress', 'done'])->default('pending');
            $table->dateTime('due_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mom_items');
    }
};
