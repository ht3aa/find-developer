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
        Schema::create('user_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('HR/Client user who created the appointment');
            $table->foreignId('developer_id')->constrained()->onDelete('cascade')->comment('Developer who has the appointment');
            $table->foreignId('user_service_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamp('start_datetime')->nullable()->comment('Date and time when the appointment starts');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_appointments');
    }
};
