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
            $table->unsignedBigInteger('current_team_id_to_vote')->nullable()->after('end_date');

            $table->foreign('current_team_id_to_vote', 'hackathons_current_team_id_fk')
                ->references('id')
                ->on('hackathon_teams')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathons', function (Blueprint $table) {
            $table->dropForeign('hackathons_current_team_id_fk');
            $table->dropColumn('current_team_id_to_vote');
        });
    }
};
