<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_jobs', function (Blueprint $table) {
            $table->dropForeign(['job_title_id']);
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('job_title_id')->nullable()->change();
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $firstJobTitleId = DB::table('job_titles')->orderBy('id')->value('id');

        if ($firstJobTitleId !== null) {
            DB::table('company_jobs')->whereNull('job_title_id')->update(['job_title_id' => $firstJobTitleId]);
        }

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->dropForeign(['job_title_id']);
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('job_title_id')->nullable(false)->change();
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->cascadeOnDelete();
        });
    }
};
