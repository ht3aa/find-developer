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
        Schema::create('badge_user_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_service_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['badge_id', 'user_service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_user_service');
    }
};
