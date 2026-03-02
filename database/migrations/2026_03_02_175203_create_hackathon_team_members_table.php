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
        Schema::create('hackathon_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_id')->constrained()->cascadeOnDelete();
            $table->string('position', 50);
            $table->timestamps();

            $table->unique(['hackathon_team_id', 'developer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_team_members');
    }
};
