<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('title');
            $table->enum('repeat_type', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('repeat_interval')->default(1); // every N days/weeks/months
            $table->timestamp('last_done_at')->nullable();
            $table->timestamp('next_due_at');
            $table->integer('reminder_before_days')->nullable()->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_schedules');
    }
};
