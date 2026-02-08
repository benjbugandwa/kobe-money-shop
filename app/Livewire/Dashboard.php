<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cotisation;
use App\Models\Emprunt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Services\CaisseService;


class Dashboard extends Component
{
    protected CaisseService $caisseService;

    public function mount(CaisseService $caisseService)
    {
        $this->caisseService = $caisseService;
    }

    public function beneficeTotal(): float
    {
        return Emprunt::where('statut_emprunt', 'remboursé')
            ->sum(DB::raw(
                'interets_payes + COALESCE(montant_penalite, 0)'
            ));
    }

    public function totalEmprunts(): float
    {
        return Emprunt::whereIn('statut_emprunt', [
            'en_retard',
            'en_cours'
        ])->sum('montant_initial');
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






    public function render()
    {
        $user = auth()->user();

        return view('livewire.dashboard', [
            'totalCotisations' => Cotisation::sum('montant'),
            'totalEmprunts'    => $this->totalEmprunts(), //Emprunt::sum('montant_initial'),
            'soldeCaisse'      => $this->soldeCaisse(),

            'mesCotisations'   => $user->cotisations()->latest()->get(),
            'mesEmprunts'      => $user->emprunts()->latest()->get(),
            'beneficeTotal' => $this->beneficeTotal(),
        ]);
    }
}
