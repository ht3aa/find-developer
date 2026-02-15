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
        Schema::table('developer_companies', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('is_current')->constrained('developer_companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
