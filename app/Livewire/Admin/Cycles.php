<?php

namespace App\Livewire\Admin;

use App\Models\Cycle;
use App\Models\Emprunt;
use App\Models\User;
use App\Services\EmpruntPenaltyService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Cycles extends Component
{
    public bool $showCreateModal = false;
    public bool $showCloseModal = true;
    public string $date_debut = '';
    public string $date_fin = '';

    public function mount(): void
    {
        $this->date_debut = now()->toDateString();
        $this->date_fin = now()->addMonth()->toDateString();
        $this->showCreateModal = ! Cycle::current();
    }

    public function render()
    {
        app(EmpruntPenaltyService::class)->refreshOpenLoans();

        $currentCycle = Cycle::current();

        return view('livewire.admin.cycles', [
            'currentCycle' => $currentCycle,
            'cycles' => Cycle::latest('date_debut')->get(),
            'redistributions' => $currentCycle ? $this->redistributions($currentCycle) : collect(),
            'totalCotisations' => $currentCycle ? $this->totalCotisations($currentCycle) : 0,
            'totalInterets' => $currentCycle ? $this->totalInterets($currentCycle) : 0,
        ]);
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->date_debut = now()->toDateString();
        $this->date_fin = now()->addMonth()->toDateString();
        $this->showCreateModal = true;
    }

    public function createCycle(): void
    {
        $this->validate([
            'date_debut' => 'required|date|before:date_fin',
            'date_fin' => 'required|date|after:today|after:date_debut',
        ], [
            'date_debut.before' => 'La date de debut doit etre inferieure a la date de fin.',
            'date_fin.after' => 'La date de fin doit etre superieure a la date actuelle et a la date de debut.',
        ]);

        if (Cycle::current()) {
            $this->toast('error', 'Impossible de creer un nouveau cycle tant qu un cycle est en cours.');
            return;
        }

        Cycle::create([
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'created_by' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->showCloseModal = true;

        $this->toast('success', 'Cycle cree avec succes.');
    }

    public function closeCurrentCycle(): void
    {
        $cycle = Cycle::current();

        if (! $cycle) {
            $this->toast('error', 'Aucun cycle en cours a cloturer.');
            return;
        }

        if ($cycle->date_fin->isFuture()) {
            $this->toast('error', 'Le cycle ne peut pas etre cloture avant sa date de fin.');
            return;
        }

        $openLoans = Emprunt::where('cycle_id', $cycle->id)
            ->where('statut_emprunt', '!=', 'remboursé')
            ->count();

        if ($openLoans > 0) {
            $this->toast('error', 'Le cycle contient encore des emprunts non rembourses.');
            return;
        }

        $cycle->update([
            'statut' => Cycle::STATUT_CLOTURE,
        ]);

        $this->showCloseModal = false;
        $this->toast('success', 'Cycle cloture avec succes.');
    }

    private function redistributions(Cycle $cycle)
    {
        $totalCotisations = $this->totalCotisations($cycle);
        $totalInterets = $this->totalInterets($cycle);

        return User::query()
            ->withSum([
                'cotisations as total_cotisations_cycle' => fn ($query) => $query->where('cycle_id', $cycle->id),
            ], 'montant')
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user) => (float) $user->total_cotisations_cycle > 0)
            ->map(function (User $user) use ($totalCotisations, $totalInterets) {
                $cotisations = (float) $user->total_cotisations_cycle;
                $pourcentage = $totalCotisations > 0 ? ($cotisations * 100 / $totalCotisations) : 0;
                $montant = $totalCotisations > 0 ? ($cotisations / $totalCotisations) * $totalInterets : 0;

                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'cotisations' => $cotisations,
                    'pourcentage' => $pourcentage,
                    'montant' => $montant,
                ];
            });
    }

    private function totalCotisations(Cycle $cycle): float
    {
        return (float) DB::table('cotisations')
            ->where('cycle_id', $cycle->id)
            ->sum('montant');
    }

    private function totalInterets(Cycle $cycle): float
    {
        return (float) DB::table('emprunts')
            ->where('cycle_id', $cycle->id)
            ->where('statut_emprunt', 'remboursé')
            ->sum(DB::raw('interets_payes + COALESCE(montant_penalite, 0)'));
    }

    private function toast(string $type, string $message): void
    {
        $this->dispatch('toast', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}
