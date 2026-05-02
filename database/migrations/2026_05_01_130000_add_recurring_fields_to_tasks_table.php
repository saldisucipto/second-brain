<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('due_date');
            $table->enum('repeat_type', ['daily', 'weekly', 'monthly'])->nullable()->after('is_recurring');
            $table->unsignedInteger('repeat_interval')->default(1)->after('repeat_type');
            $table->timestamp('last_generated_at')->nullable()->after('repeat_interval');
        });

        Schema::create('moms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('meeting_date');
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_group_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'ongoing', 'closed'])->default('draft');
            $table->timestamps();
        });

        Schema::create('mom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mom_id')->constrained()->cascadeOnDelete();
            $table->text('action');
            $table->string('pic')->nullable();
            $table->enum('status', ['pending', 'progress', 'done'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('mom_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mom_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mom_attachments');
        Schema::dropIfExists('mom_items');
        Schema::dropIfExists('moms');

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'is_recurring',
                'repeat_type',
                'repeat_interval',
                'last_generated_at',
            ]);
        });
    }
};
