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
        Schema::table('hackathons', function (Blueprint $table) {
            $table->foreignId('reward_badge_id')->nullable()->after('youtube_url')->constrained('badges')->nullOnDelete();
            $table->text('reward_description')->nullable()->after('reward_badge_id');
            $table->date('start_date')->nullable()->after('reward_description');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathons', function (Blueprint $table) {
            $table->dropForeign(['reward_badge_id']);
            $table->dropColumn(['reward_description', 'start_date', 'end_date']);
        });
    }
};
