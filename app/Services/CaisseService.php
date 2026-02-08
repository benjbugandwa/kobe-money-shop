<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Emprunt;
use Illuminate\Support\Facades\DB;

class CaisseService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function soldeCaisse(): float
    {
        $totalCotisations = Cotisation::sum('montant');

        $capitalEncoreSorti = Emprunt::whereIn('statut_emprunt', [
            'accorde',
            'en_cours'
        ])->sum('montant_initial');

        $interetsEtPenalitesEncaissees = Emprunt::where('statut_emprunt', 'remboursé')
            ->sum(DB::raw(
                'montant_final - montant_initial + COALESCE(montant_penalite, 0)'
            ));

        return $totalCotisations
            + $interetsEtPenalitesEncaissees
            - $capitalEncoreSorti;
    }

    public function beneficeTotal(): float
    {
        return Emprunt::where('statut_emprunt', 'remboursé')
            ->sum(DB::raw(
                'montant_final - montant_initial + COALESCE(montant_penalite, 0)'
            ));
    }
}
