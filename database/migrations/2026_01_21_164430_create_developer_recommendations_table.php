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
        Schema::create('developer_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommender_id')->constrained('developers')->cascadeOnDelete();
            $table->foreignId('recommended_id')->constrained('developers')->cascadeOnDelete();
            $table->timestamps();

            // Prevent a developer from recommending the same developer twice
            $table->unique(['recommender_id', 'recommended_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_recommendations');
    }
};
