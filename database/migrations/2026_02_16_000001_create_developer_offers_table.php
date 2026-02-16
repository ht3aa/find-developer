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
        Schema::create('developer_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('developers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->foreignId('job_title_id')->constrained('job_titles')->cascadeOnDelete();
            $table->text('message');
            $table->string('salary_range')->nullable();
            $table->string('work_type')->nullable();
            $table->string('contact_email');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_offers');
    }
};
