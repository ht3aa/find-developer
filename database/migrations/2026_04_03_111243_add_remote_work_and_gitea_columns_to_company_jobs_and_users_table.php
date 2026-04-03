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
        Schema::table('company_jobs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->boolean('first_payment_qi_confirmed')->default(false)->after('status');
            $table->string('gitea_owner')->nullable();
            $table->string('gitea_repo_name')->nullable();
            $table->timestamp('gitea_provisioned_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('gitea_username')->nullable()->after('linkedin_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gitea_username');
        });

        Schema::table('company_jobs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'first_payment_qi_confirmed',
                'gitea_owner',
                'gitea_repo_name',
                'gitea_provisioned_at',
            ]);
        });
    }
};
