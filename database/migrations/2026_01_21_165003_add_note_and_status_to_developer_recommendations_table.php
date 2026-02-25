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
        Schema::table('developer_recommendations', function (Blueprint $table) {
            $table->text('recommendation_note')->nullable()->after('recommended_id');
            $table->string('status')->default('pending')->after('recommendation_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_recommendations', function (Blueprint $table) {
            $table->dropColumn(['recommendation_note', 'status']);
        });
    }
};
