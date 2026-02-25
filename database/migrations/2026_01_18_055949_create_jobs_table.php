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
        Schema::create('company_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('company_name');
            $table->string('email');
            $table->string('contact_link')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('job_title_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('salary_from')->nullable();
            $table->unsignedBigInteger('salary_to')->nullable();
            $table->string('salary_currency')->default('IQD');
            $table->text('requirements')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_jobs');
    }
};
