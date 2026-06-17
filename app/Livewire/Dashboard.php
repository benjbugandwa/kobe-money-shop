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

        $totalCotisationsCycleEnCours = $this->totalCotisationsCycleEnCours($currentCycleId);
        $capitalEmpruntsOuvertsCycleEnCours = $this->capitalEmpruntsOuvertsCycleEnCours($currentCycleId);
        $interetsPayesTousEmpruntsCycleEnCours = $this->interetsPayesTousEmpruntsCycleEnCours($currentCycleId);
        $interetsAttendusEmpruntsOuvertsCycleEnCours = $this->interetsAttendusEmpruntsOuvertsCycleEnCours($currentCycleId);
        $soldeCaisseCycleEnCours = $this->soldeCaisseCycleEnCours($currentCycleId);

        return view('livewire.dashboard', [
            'cycleEnCours' => $currentCycle,
            'cotisationsCyclesClotures' => $cotisationsCyclesClotures,
            'cotisationsCycleEnCours' => $cotisationsCycleEnCours,
            'totalCotisationsMembre' => $totalCotisationsMembre,
            'totalCotisationsCycleEnCours' => $totalCotisationsCycleEnCours,
            'totalEmpruntsCycleEnCours' => $this->totalEmpruntsCycleEnCours($currentCycleId, $user->id),
            'interetsPayesCycleEnCours' => $this->interetsPayesCycleEnCours($currentCycleId, $user->id),
            'interetsPayesTousEmpruntsCycleEnCours' => $interetsPayesTousEmpruntsCycleEnCours,
            'interetsAttendusEmpruntsOuvertsCycleEnCours' => $interetsAttendusEmpruntsOuvertsCycleEnCours,
            'capitalEmpruntsOuvertsCycleEnCours' => $capitalEmpruntsOuvertsCycleEnCours,
            'soldeCaisseCycleEnCours' => $soldeCaisseCycleEnCours,
            'tauxDemandeEmpruntsCycleEnCours' => $this->percentage($capitalEmpruntsOuvertsCycleEnCours, $totalCotisationsCycleEnCours),
            'tauxRendementInteretsCycleEnCours' => $this->percentage($interetsPayesTousEmpruntsCycleEnCours, $totalCotisationsCycleEnCours),
            'partCotisationsMembreCycleEnCours' => $this->percentage($cotisationsCycleEnCours, $totalCotisationsCycleEnCours),
            'tauxCouvertureCaisseCycleEnCours' => $this->percentage($soldeCaisseCycleEnCours, $totalCotisationsCycleEnCours),
            'mesCotisationsCycleEnCours' => $mesCotisationsCycleEnCours,
            'mesEmpruntsCycleEnCours' => $mesEmpruntsCycleEnCours,
        ]);
    }

    private function totalCotisationsCycleEnCours(?int $cycleId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Cotisation::where('cycle_id', $cycleId)->sum('montant');
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

    private function capitalEmpruntsOuvertsCycleEnCours(?int $cycleId): float
    {
        if (! $cycleId) {
            return 0;
        }

        return (float) Emprunt::where('cycle_id', $cycleId)
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

    private function percentage(float $value, float $base): float
    {
        if ($base <= 0) {
            return 0;
        }

        return round(($value / $base) * 100, 1);
    }
}
