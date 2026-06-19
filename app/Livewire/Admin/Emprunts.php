<?php

namespace App\Livewire\Admin;

use App\Models\Cycle;
use App\Models\Emprunt;
use App\Models\User;
use App\Services\CaisseService;
use App\Services\EmpruntPenaltyService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Emprunts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $showModal = false;
    public $user_id;
    public $date_emprunt;
    public $date_echeance;
    public $montant_initial;
    public $taux_interet = 5;
    public $montant_final;
    public $observation;

    public $confirmingStatutChange = false;
    public $empruntIdToUpdate;

    public $filterStatut = '';
    public $filterUser = '';

    public function render()
    {
        app(EmpruntPenaltyService::class)->refreshOpenLoans();

        $query = Emprunt::with(['user', 'cycle'])
            ->orderBy('date_emprunt', 'desc');

        if ($this->filterStatut) {
            $query->where('statut_emprunt', $this->filterStatut);
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        return view('livewire.admin.emprunts', [
            'emprunts' => $query->paginate(10),
            'users' => User::orderBy('name')->get(),
            'cycleEnCours' => Cycle::current(),
        ]);
    }

    public function updatedMontantInitial(): void
    {
        $this->calculateMontantFinal();
    }

    public function updatedTauxInteret(): void
    {
        $this->calculateMontantFinal();
    }

    public function updatedDateEmprunt($value): void
    {
        if ($value) {
            $this->date_echeance = Carbon::parse($value)
                ->addDays(30)
                ->toDateString();
        }
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'user_id',
            'date_echeance',
            'montant_initial',
            'montant_final',
            'observation',
        ]);

        $this->date_emprunt = now()->toDateString();
        $this->date_echeance = now()->addDays(30)->toDateString();
        $this->taux_interet = 5;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->calculateMontantFinal();

        $this->validate([
            'user_id' => 'required|exists:users,id',
            'date_emprunt' => 'required|date|before_or_equal:today',
            'date_echeance' => 'required|date|after:date_emprunt',
            'montant_initial' => 'required|numeric|min:20',
            'taux_interet' => 'required|numeric|min:0',
            'montant_final' => 'required|numeric|min:1',
            'observation' => 'nullable|string|max:500',
        ]);

        $cycle = Cycle::current();

        if (! $cycle) {
            $this->toast('error', 'Aucun cycle en cours. Creez un cycle avant d enregistrer un emprunt.');
            return;
        }

        $solde = $this->soldeCaisse();

        if ((float) $this->montant_initial > $solde) {
            $this->addError(
                'montant_initial',
                'Le montant demande depasse le solde actuel de la caisse ('
                    . number_format($solde, 0, ',', ' ')
                    . ' USD).'
            );
            return;
        }

        Emprunt::create([
            'user_id' => $this->user_id,
            'cycle_id' => $cycle->id,
            'date_emprunt' => $this->date_emprunt,
            'date_echeance' => $this->date_echeance,
            'montant_initial' => $this->montant_initial,
            'taux_interet' => $this->taux_interet,
            'montant_final' => $this->montant_final,
            'statut_emprunt' => 'en_cours',
            'montant_penalite' => 0,
            'observation' => $this->observation,
        ]);

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
        $this->date_echeance = now()->addDays(30)->toDateString();
        $this->taux_interet = 5;
        $this->showModal = false;
        $this->resetPage();

        $this->toast('success', 'Emprunt enregistre avec succes.');
    }

    public function confirmStatutChange($empruntId): void
    {
        $emprunt = Emprunt::findOrFail($empruntId);

        if (! in_array($emprunt->statut_emprunt, ['en_cours', 'en_retard'], true)) {
            $this->toast('error', 'Cet emprunt est deja rembourse.');
            return;
        }

        $this->empruntIdToUpdate = $empruntId;
        $this->confirmingStatutChange = true;
    }

    public function changeStatut(): void
    {
        app(EmpruntPenaltyService::class)->refreshOpenLoans();

        $emprunt = Emprunt::findOrFail($this->empruntIdToUpdate);

        if (! in_array($emprunt->statut_emprunt, ['en_cours', 'en_retard'], true)) {
            $this->confirmingStatutChange = false;
            $this->empruntIdToUpdate = null;
            $this->toast('error', 'Cet emprunt est deja rembourse.');
            return;
        }

        $emprunt->update([
            'statut_emprunt' => 'remboursé',
            'interets_payes' => $emprunt->calculerInterets(),
            'statut_modifie_par' => auth()->id(),
            'statut_modifie_le' => now(),
        ]);

        $this->confirmingStatutChange = false;
        $this->empruntIdToUpdate = null;

        $this->toast('success', 'Statut de l emprunt mis a jour.');
    }

    public function updatedFilterStatut(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUser(): void
    {
        $this->resetPage();
    }

    protected function soldeCaisse(): float
    {
        return app(CaisseService::class)->soldeCaisse();
    }

    private function calculateMontantFinal(): void
    {
        if ($this->montant_initial !== null && $this->taux_interet !== null) {
            $this->montant_final = round(
                (float) $this->montant_initial
                + ((float) $this->montant_initial * (float) $this->taux_interet / 100),
                2
            );
        }
    }

    private function toast(string $type, string $message): void
    {
        $this->dispatch('toast', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}
