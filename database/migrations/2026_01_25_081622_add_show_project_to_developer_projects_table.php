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
        Schema::table('developer_projects', function (Blueprint $table) {
            $table->boolean('show_project')->default(true)->after('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_projects', function (Blueprint $table) {
            $table->dropColumn('show_project');
        });
    }
};
