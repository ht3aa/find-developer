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
        Schema::table('hackathon_subscribers', function (Blueprint $table) {
            $table->boolean('is_attended')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathon_subscribers', function (Blueprint $table) {
            $table->dropColumn('is_attended');
        });
    }
};
