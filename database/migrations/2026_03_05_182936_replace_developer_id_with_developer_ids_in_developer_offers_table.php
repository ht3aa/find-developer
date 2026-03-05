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
        Schema::table('developer_offers', function (Blueprint $table) {
            $table->json('developer_ids')->nullable()->after('id');
        });

        DB::table('developer_offers')->orderBy('id')->each(function ($row) {
            $developerId = $row->developer_id ?? null;
            if ($developerId !== null) {
                DB::table('developer_offers')
                    ->where('id', $row->id)
                    ->update(['developer_ids' => json_encode([(int) $developerId])]);
            }
        });

        Schema::table('developer_offers', function (Blueprint $table) {
            $table->dropForeign(['developer_id']);
            $table->dropColumn('developer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_offers', function (Blueprint $table) {
            $table->foreignId('developer_id')->nullable()->after('id')->constrained('developers')->cascadeOnDelete();
        });

        DB::table('developer_offers')->orderBy('id')->each(function ($row) {
            $ids = json_decode($row->developer_ids ?? '[]', true);
            $firstId = is_array($ids) && count($ids) > 0 ? (int) $ids[0] : null;
            if ($firstId !== null) {
                DB::table('developer_offers')
                    ->where('id', $row->id)
                    ->update(['developer_id' => $firstId]);
            }
        });

        Schema::table('developer_offers', function (Blueprint $table) {
            $table->dropColumn('developer_ids');
        });
    }
};
