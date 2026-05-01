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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
