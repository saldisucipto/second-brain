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
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->text('description')->nullable()->after('note');
            $table->timestamp('next_follow_up_at')->nullable()->after('reminder_at');
            $table->timestamp('due_at')->nullable()->after('next_follow_up_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'next_follow_up_at',
                'due_at',
            ]);
        });
    }
};
