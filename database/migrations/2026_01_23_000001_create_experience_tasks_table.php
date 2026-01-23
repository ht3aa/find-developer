<?php

use App\Enums\Currency;
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
        Schema::create('experience_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedInteger('required_developers_count')->default(1);
            $table->string('status')->default('open');
            $table->unsignedBigInteger('price')->nullable();
            $table->string('price_currency')->default(Currency::IQD->value);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('experience_gain')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experience_tasks');
    }
};
