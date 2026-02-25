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
        Schema::create('experience_task_developer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_task_id')->constrained('experience_tasks')->cascadeOnDelete();
            $table->foreignId('developer_id')->constrained('developers')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['experience_task_id', 'developer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_task_developer');
    }
};
