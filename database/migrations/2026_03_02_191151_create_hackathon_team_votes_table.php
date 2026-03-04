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
        Schema::create('hackathon_team_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_developer_id')->constrained('developers')->cascadeOnDelete();
            $table->boolean('is_voted')->default(true);
            $table->timestamps();

            $table->unique(['hackathon_team_id', 'subscriber_developer_id'], 'ht_team_votes_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_team_votes');
    }
};
