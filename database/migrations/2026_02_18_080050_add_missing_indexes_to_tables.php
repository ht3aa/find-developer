<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes on columns commonly used in WHERE/ORDER BY that do not
     * already have an index (e.g. foreign keys are already indexed).
     */
    public function up(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->index('status', 'blog_comments_status_index');
        });

        Schema::table('developer_blogs', function (Blueprint $table) {
            $table->index('status', 'developer_blogs_status_index');
            $table->index('published_at', 'developer_blogs_published_at_index');
        });

        Schema::table('developer_offers', function (Blueprint $table) {
            $table->index('status', 'developer_offers_status_index');
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->index('status', 'company_jobs_status_index');
        });

        Schema::table('developers', function (Blueprint $table) {
            $table->index('status', 'developers_status_index');
            $table->index('is_available', 'developers_is_available_index');
        });

        Schema::table('user_appointments', function (Blueprint $table) {
            $table->index('status', 'user_appointments_status_index');
            $table->index('start_datetime', 'user_appointments_start_datetime_index');
        });

        Schema::table('user_services', function (Blueprint $table) {
            $table->index('is_active', 'user_services_is_active_index');
        });

        Schema::table('experience_tasks', function (Blueprint $table) {
            $table->index('status', 'experience_tasks_status_index');
        });

        Schema::table('developer_recommendations', function (Blueprint $table) {
            $table->index('status', 'developer_recommendations_status_index');
        });

        Schema::table('developer_projects', function (Blueprint $table) {
            $table->index('show_project', 'developer_projects_show_project_index');
        });

        Schema::table('developer_companies', function (Blueprint $table) {
            $table->index('show_company', 'developer_companies_show_company_index');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->index('is_active', 'badges_is_active_index');
        });

        Schema::table('job_titles', function (Blueprint $table) {
            $table->index('is_active', 'job_titles_is_active_index');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->index('is_active', 'skills_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_comments', function (Blueprint $table) {
            $table->dropIndex('blog_comments_status_index');
        });

        Schema::table('developer_blogs', function (Blueprint $table) {
            $table->dropIndex('developer_blogs_status_index');
            $table->dropIndex('developer_blogs_published_at_index');
        });

        Schema::table('developer_offers', function (Blueprint $table) {
            $table->dropIndex('developer_offers_status_index');
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->dropIndex('company_jobs_status_index');
        });

        Schema::table('developers', function (Blueprint $table) {
            $table->dropIndex('developers_status_index');
            $table->dropIndex('developers_is_available_index');
        });

        Schema::table('user_appointments', function (Blueprint $table) {
            $table->dropIndex('user_appointments_status_index');
            $table->dropIndex('user_appointments_start_datetime_index');
        });

        Schema::table('user_services', function (Blueprint $table) {
            $table->dropIndex('user_services_is_active_index');
        });

        Schema::table('experience_tasks', function (Blueprint $table) {
            $table->dropIndex('experience_tasks_status_index');
        });

        Schema::table('developer_recommendations', function (Blueprint $table) {
            $table->dropIndex('developer_recommendations_status_index');
        });

        Schema::table('developer_projects', function (Blueprint $table) {
            $table->dropIndex('developer_projects_show_project_index');
        });

        Schema::table('developer_companies', function (Blueprint $table) {
            $table->dropIndex('developer_companies_show_company_index');
        });

        Schema::table('badges', function (Blueprint $table) {
            $table->dropIndex('badges_is_active_index');
        });

        Schema::table('job_titles', function (Blueprint $table) {
            $table->dropIndex('job_titles_is_active_index');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex('skills_is_active_index');
        });
    }
};
