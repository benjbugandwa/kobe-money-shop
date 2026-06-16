<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotisations', function (Blueprint $table) {
            $table->foreignId('cycle_id')
                ->nullable()
                ->after('user_id')
                ->constrained('cycles')
                ->nullOnDelete();
        });

        Schema::table('emprunts', function (Blueprint $table) {
            $table->foreignId('cycle_id')
                ->nullable()
                ->after('user_id')
                ->constrained('cycles')
                ->nullOnDelete();
        });

        $oldestCotisation = DB::table('cotisations')->min('date_cotisation');
        $oldestEmprunt = DB::table('emprunts')->min('date_emprunt');
        $oldestTransactionDate = collect([$oldestCotisation, $oldestEmprunt])
            ->filter()
            ->sort()
            ->first();

        if ($oldestTransactionDate) {
            $creatorId = DB::table('users')
                ->orderByRaw("CASE WHEN user_role = 'admin' THEN 0 ELSE 1 END")
                ->orderBy('id')
                ->value('id');

            $cycleId = DB::table('cycles')->insertGetId([
                'num_cycle' => 'CYL-0001',
                'date_debut' => $oldestTransactionDate,
                'date_fin' => '2026-08-31',
                'created_by' => $creatorId,
                'statut' => 'En cours',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('cotisations')
                ->whereNull('cycle_id')
                ->update(['cycle_id' => $cycleId]);

            DB::table('emprunts')
                ->whereNull('cycle_id')
                ->update(['cycle_id' => $cycleId]);
        }
    }

    public function down(): void
    {
        Schema::table('emprunts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cycle_id');
        });

        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cycle_id');
        });
    }
};
