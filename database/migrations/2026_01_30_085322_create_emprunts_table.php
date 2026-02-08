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
        Schema::create('emprunts', function (Blueprint $table) {
            $table->id();

            $table->date('date_emprunt');
            $table->date('date_echeance');

            $table->decimal('montant_initial', 15, 2);
            $table->decimal('taux_interet', 5, 2)->default(10);

            $table->decimal('montant_final', 15, 2)->nullable();
            $table->decimal('montant_penalite', 15, 2)->default(0);

            $table->enum('statut_emprunt', [
                'en_cours',
                'remboursé',
                'en_retard'
            ])->default('en_cours');

            $table->text('observation')->nullable();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emprunts');
    }
};
