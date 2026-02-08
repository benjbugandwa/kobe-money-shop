<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Emprunt;
use App\Models\User;
use App\Models\Cotisation;
use Illuminate\Support\Facades\DB;
use App\Services\CaisseService;

class Emprunts extends Component
{
    protected CaisseService $caisseService;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $showModal = false;

    public $user_id;
    public $date_emprunt;
    public $date_echeance;
    public $montant_initial;
    public $taux_interet = 5; // 👈 par défaut 5%
    public $montant_final;
    public $observation;

    public $confirmingStatutChange = false;
    public $empruntIdToUpdate;

    //Filtre
    public $filterStatut = '';
    public $filterUser = '';



    public function mount(CaisseService $caisseService)
    {
        $this->caisseService = $caisseService;
    }



    public function render()
    {
        $query = Emprunt::with('user')
            ->orderBy('date_emprunt', 'desc');

        if ($this->filterStatut) {
            $query->where('statut_emprunt', $this->filterStatut);
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        return view('livewire.admin.emprunts', [
            'emprunts' => $query->paginate(10),
            'users'    => User::orderBy('name')->get(),
        ]);
    }








    public function updatedMontantInitial()
    {
        $this->calculateMontantFinal();
    }

    public function updatedTauxInteret()
    {
        $this->calculateMontantFinal();
    }

    private function calculateMontantFinal()
    {
        if ($this->montant_initial && $this->taux_interet) {
            $this->montant_final =
                $this->montant_initial +
                ($this->montant_initial * $this->taux_interet / 100);
        }
    }

    public function save()
    {





        $this->validate([
            'user_id'          => 'required|exists:users,id',
            'date_emprunt'     => 'required|date|before_or_equal:today',
            'date_echeance'    => 'required|date|after:date_emprunt',
            'montant_initial'  => 'required|numeric|min:20',
            'taux_interet'     => 'required|numeric|min:0',
            'montant_final'    => 'required|numeric|min:1',
            'observation'      => 'nullable|string|max:500',
        ]);

        \App\Models\Emprunt::create([
            'user_id'          => $this->user_id,
            'date_emprunt'     => $this->date_emprunt,
            'date_echeance'    => $this->date_echeance,
            'montant_initial'  => $this->montant_initial,
            'taux_interet'     => $this->taux_interet,
            'montant_final'    => $this->montant_final,
            'statut_emprunt'   => 'en_cours',
            'montant_penalite' => 0,
            'observation'      => $this->observation,
        ]);


        $solde = $this->soldeCaisse();

        if ($this->montant_initial > $solde) {
            $this->addError(
                'newMontantInitial',
                'Le montant demandé dépasse le solde actuel de la caisse ('
                    . number_format($solde, 0, ',', ' ')
                    . ' USD).'
            );
            return;
        }


        $this->reset([
            'user_id',
            'date_emprunt',
            'date_echeance',
            'montant_initial',
            'taux_interet',
            'montant_final',
            'observation',
        ]);

        $this->date_emprunt = now()->toDateString();
        $this->taux_interet = 5;
        $this->showModal = false;

        session()->flash('success', 'Emprunt enregistré avec succès.');
    }

    public function confirmStatutChange($empruntId)
    {
        $emprunt = \App\Models\Emprunt::findOrFail($empruntId);

        // ❌ Un admin ne peut pas modifier son propre emprunt
        if ($emprunt->user_id === auth()->id()) {
            session()->flash('error', "Vous ne pouvez pas modifier votre propre emprunt.");
            return;
        }

        $this->empruntIdToUpdate = $empruntId;
        $this->confirmingStatutChange = true;
    }

    public function changeStatut()
    {
        $emprunt = \App\Models\Emprunt::findOrFail($this->empruntIdToUpdate);

        if ($emprunt->user_id === auth()->id()) {
            abort(403);
        }

        $interets = $emprunt->calculerInterets();

        $emprunt->update([
            'statut_emprunt'     => 'remboursé',
            'interets_payes'   => $interets,
            'statut_modifie_par' => auth()->id(),
            'statut_modifie_le'  => now(),
        ]);

        $this->confirmingStatutChange = false;
        $this->empruntIdToUpdate = null;

        session()->flash('success', 'Statut de l’emprunt mis à jour.');
    }

    public function updatedFilterStatut()
    {
        $this->resetPage();
    }

    public function updatedFilterUser()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->reset([
            'user_id',
            'date_echeance',
            'montant_initial',
            'montant_final',
            'observation',
        ]);

        $this->date_emprunt = now()->toDateString(); // 👈 aujourd’hui
        $this->taux_interet = 5; // sécurité
        $this->showModal = true;
    }

    public function updatedDateEmprunt($value)
    {
        if ($value) {
            $this->date_echeance = \Carbon\Carbon::parse($value)
                ->addDays(30)
                ->toDateString();
        }
    }

    //Verifie la disponibilité de la caisse
    protected function soldeCaisse1(): float
    {
        $totalCotisations = Cotisation::sum('montant');

        $totalEmprunts = Emprunt::whereIn('statut_emprunt', [
            'accorde',
            'en_cours'
        ])->sum('montant_initial');

        return $totalCotisations - $totalEmprunts;
    }



    /*
        Quand un emprunt est remboursé, la caisse reçoit :
        Ces montants augmentent le solde réel de la caisse.
        Donc:

        Le solde réel = Cotisations
                        + Intérêts encaissés
                        + Pénalités encaissées
                        - Capital encore sorti (emprunts en cours ou accordés)
    */
    protected function soldeCaisse(): float
    {
        $totalCotisations = Cotisation::sum('montant');

        $capitalEncoreSorti = Emprunt::whereIn('statut_emprunt', [
            'accorde',
            'en_cours'
        ])->sum('montant_initial');

        $interetsEtPenalitesEncaissees = Emprunt::where('statut_emprunt', 'rembourse')
            ->sum(DB::raw('montant_final - montant_initial + COALESCE(montant_penalite,0)'));

        return $totalCotisations
            + $interetsEtPenalitesEncaissees
            - $capitalEncoreSorti;
    }
}
