<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Emprunt;
use Illuminate\Support\Facades\DB;

class CaisseService
{
    public function __construct(
        private readonly EmpruntPenaltyService $penaltyService
    ) {
    }

    public function soldeCaisse(): float
    {
        $this->penaltyService->refreshOpenLoans();

        $totalCotisations = Cotisation::sum('montant');

        $capitalEncoreSorti = Emprunt::whereIn('statut_emprunt', [
            'en_cours',
            'en_retard',
        ])->sum('montant_initial');

        $interetsEtPenalitesEncaissees = Emprunt::where('statut_emprunt', 'remboursé')
            ->sum(DB::raw(
                'interets_payes + COALESCE(montant_penalite, 0)'
            ));

        return $totalCotisations
            + $interetsEtPenalitesEncaissees
            - $capitalEncoreSorti;
    }

    public function beneficeTotal(): float
    {
        $this->penaltyService->refreshOpenLoans();

        return Emprunt::where('statut_emprunt', 'remboursé')
            ->sum(DB::raw(
                'interets_payes + COALESCE(montant_penalite, 0)'
            ));
    }
}
