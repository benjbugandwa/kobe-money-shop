<?php

namespace App\Livewire;

use App\Models\Cotisation;
use App\Models\Cycle;
use App\Models\Emprunt;
use App\Services\EmpruntPenaltyService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        app(EmpruntPenaltyService::class)->refreshOpenLoans();

        $user = auth()->user();
        $currentCycle = Cycle::current();
        $currentCycleId = $currentCycle?->id;

        $cotisationsCyclesClotures = (float) $user->cotisations()
            ->whereHas('cycle', fn ($query) => $query->where('statut', Cycle::STATUT_CLOTURE))
            ->sum('montant');

        $cotisationsCycleEnCours = $currentCycleId
            ? (float) $user->cotisations()->where('cycle_id', $currentCycleId)->sum('montant')
            : 0;

        $totalCotisationsMembre = $cotisationsCyclesClotures + $cotisationsCycleEnCours;

        $mesCotisationsCycleEnCours = $currentCycleId
            ? $user->cotisations()
                ->with('cycle')
                ->where('cycle_id', $currentCycleId)
                ->latest('date_cotisation')
                ->get()
            : collect();

        $mesEmpruntsCycleEnCours = $currentCycleId
            ? $user->emprunts()
                ->with('cycle')
                ->where('cycle_id', $currentCycleId)
                ->latest('date_emprunt')
                ->get()
            : collect();

        return view('livewire.dashboard', [
            'cycleEnCours' => $currentCycle,
            'cotisationsCyclesClotures' => $cotisationsCyclesClotures,
            'cotisationsCycleEnCours' => $cotisationsCycleEnCours,
            'totalCotisationsMembre' => $totalCotisationsMembre,
            'totalEmpruntsCycleEnCours' => $this->totalEmpruntsCycleEnCours($currentCycleId, $user->id),
            'interetsPayesCycleEnCours' => $this->interetsPayesCycleEnCours($currentCycleId, $user->id),
            'interetsPayesTousEmpruntsCycleEnCours' => $this->interetsPayesTousEmpruntsCycleEnCours($currentCycleId),
            'interetsAttendusEmpruntsOuvertsCycleEnCours' => $this->interetsAttendusEmpruntsOuvertsCycleEnCours($currentCycleId),
            'soldeCaisseCycleEnCours' => $this->soldeCaisseCycleEnCours($currentCycleId),
            'mesCotisationsCycleEnCours' => $mesCotisationsCycleEnCours,
            'mesEmpruntsCycleEnCours' => $mesEmpruntsCycleEnCours,
        ]);
    }

    private function totalEmpruntsCycleEnCours(?int $cycleId, int $userId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Emprunt::where('cycle_id', $cycleId)
            ->where('user_id', $userId)
            ->whereIn('statut_emprunt', ['en_cours', 'en_retard'])
            ->sum('montant_initial');
    }

    private function interetsPayesCycleEnCours(?int $cycleId, int $userId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Emprunt::where('cycle_id', $cycleId)
            ->where('user_id', $userId)
            ->where('statut_emprunt', 'remboursé')
            ->sum(DB::raw('interets_payes + COALESCE(montant_penalite, 0)'));
    }

    private function interetsPayesTousEmpruntsCycleEnCours(?int $cycleId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Emprunt::where('cycle_id', $cycleId)
            ->where('statut_emprunt', 'remboursé')
            ->sum(DB::raw('interets_payes + COALESCE(montant_penalite, 0)'));
    }

    private function interetsAttendusEmpruntsOuvertsCycleEnCours(?int $cycleId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Emprunt::where('cycle_id', $cycleId)
            ->whereIn('statut_emprunt', ['en_cours', 'en_retard'])
            ->sum(DB::raw('montant_initial * taux_interet / 100'));
    }

    private function soldeCaisseCycleEnCours(?int $cycleId): float
    {
        if (! $cycleId) {
            return 0;
        }

        $totalCotisations = (float) Cotisation::where('cycle_id', $cycleId)->sum('montant');

        $capitalEncoreSorti = (float) Emprunt::where('cycle_id', $cycleId)
            ->whereIn('statut_emprunt', ['en_cours', 'en_retard'])
            ->sum('montant_initial');

        $interetsEtPenalitesEncaissees = (float) Emprunt::where('cycle_id', $cycleId)
            ->where('statut_emprunt', 'remboursé')
            ->sum(DB::raw('interets_payes + COALESCE(montant_penalite, 0)'));

        return $totalCotisations + $interetsEtPenalitesEncaissees - $capitalEncoreSorti;
    }
}
